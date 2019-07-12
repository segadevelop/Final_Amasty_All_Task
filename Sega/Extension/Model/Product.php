<?php
/**
 * Global Functional To Work Extension
 *
 * Sega_Dev >>>>>>>>>>> Amasty Extension Work Enterprise
 *
 * Model Logic:
 *
 */

namespace Sega\Extension\Model;

/**
 * All Resource Class Libs To Work Model Resource
 */
use Magento\Catalog\Api\ProductRepositoryInterface,
    Magento\Framework\Data\Form\FormKey,
    Magento\Checkout\Model\Cart,
    Magento\Framework\View\Result\PageFactory,
    Magento\Framework\Controller\Result\JsonFactory,
    Magento\Framework\App\Request\Http,
    Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollection,
    Magento\Framework\Message\ManagerInterface,
    Magento\Checkout\Controller\Cart\Add,
    Magento\Framework\App\Config\ScopeConfigInterface,
    Magento\Framework\Exception\NoSuchEntityException,
    Magento\Framework\DataObject\IdentityInterface,
    Magento\Framework\Model\AbstractModel,
    Magento\Framework\Model\ResourceModel\Db\AbstractDb,
    Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection,
    Magento\Framework\File\Csv;

use Magento\Framework\Data\CollectionFactory as DataCollection;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Product
 * @package Sega\Extension\Model
 */
class Product
{
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var Http
     */
    private $request;

    /**
     * @var FormKey
     */
    private $formKey;

    /**
     * @var Cart
     */
    private $cart;

    /**
     * @var JsonFactory
     */
    private $jsonFactory;

    /**
     * @var CollectionFactory
     */
    private $productCollection;

    /**
     * @var PageFactory
     */
    private $pageFactory;

    protected $csvImport;

    protected $message;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        FormKey $formKey,
        Cart $cart,
        Http $request,
        JsonFactory $jsonFactory,
        Csv $csvImport,
        ManagerInterface $message,
        ProductCollection $productCollection,
        PageFactory $pageFactory
    ) {
        $this->productRepository = $productRepository;
        $this->formKey = $formKey;
        $this->cart = $cart;
        $this->request = $request;
        $this->jsonFactory = $jsonFactory;
        $this->csvImport = $csvImport;
        $this->message = $message;
        $this->productCollection = $productCollection;
        $this->pageFactory = $pageFactory;
    }

    /**
     * @param $productObj
     * @param $params
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function addCustomProduct($product, $params)
    {
        $this->cart->addProduct($product, $params);
        $this->cart->save();
    }

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function addProductBySku()
    {
        $request = $this->request->getMethod();
        switch ($request)
        {
            case 'POST':
                $value = $this->request->getParam('search_sku');
                    try {
                        $product = $this->productRepository->get($value);
                        $productId = $product->getId();
                        $params = [
                            'form_key'     => $this->formKey->getFormKey(),
                            'product_id'   => $productId,
                            'qty'          => 1
                        ];
                        $this->addCustomProduct($product, $params);
                    }
                    catch (\Exception $e){
                        return 'Error';
                    }
                break;
            default:
            case 'GET':
                //
                break;
        }
    }

    /**
     * @param $sku
     * @return array
     */
    public function findBySKU($value)
    {
        $array = [];
        $productCollection = $this->productCollection->create()
            ->addFieldToFilter('sku', ['like' => '%' . $value . '%'])
            ->setPageSize(15)
            ->setCurPage(1);

        foreach ($productCollection as $product ) {
            $array[$product->getSku()] = $product->getSku();
        }

        return $array;
    }

    public function csvImportFile()
    {
        if($file = $this->request->getFiles('csv')){

            if (!isset($file['tmp_name'])) {
                throw new LocalizedException(__('Invalid file upload attempt.'));
            }

            $importProductRawData = $this->csvImport->getData($file['tmp_name']);
            $arrayProduct = [];

            foreach ($importProductRawData as $indexRow => $dataRow) {
                $arrayProduct[] = $dataRow;
            }

            foreach ($arrayProduct as $item) {
                try {
                    $product = $this->productRepository->get($item[0]);
                    $idProduct = $product->getId();
                    $productType = $product->getTypeId();

                    if ($productType == 'simple') {
                        $params = [
                            'product' => $idProduct,
                            'qty' => $item[1]
                        ];
                        $this->cart->addProduct($product, $params);
                        $this->message->addSuccessMessage(__($item[0] . ' Товар добавлен в карзину, в количестве: ' . $item[1]));
                    } else {
                        $this->message->addWarningMessage(__("Warning"));
                        $this->request->setParam('product', null);
                    }
                } catch (\Exception $e) {
                    $this->message->addErrorMessage(__($item[0] . ' Такого товара нет'));
                }
            }
            $this->cart->save();
        }
    }

    public function setBeforeSetupAddToCart($resource)
    {
        if($this->request->getPost('search_sku')) {
            $sku = $this->request->getPost('search_sku');
            $url = $this->request->getPost('url');
            try {
                $product = $this->productRepository->get($sku);
                $idProduct = $product->getId();
                $productType = $product->getTypeId();
                if ($productType == 'simple') {
                    $params = [
                        'product' => $idProduct,
                        'qty' => 1
                    ];
                    if($url != ''){
                        $resource->setName($sku);
                        $resource->save();
                    }
                    $this->request->setParams($params);
                } else {
                    $this->message->addWarningMessage(__("Warning"));
                    $this->request->setParam('product', null);
                }
            } catch (\Exception $e) {
                $this->message->addErrorMessage(__("Error"));
            }
        }
    }

}
