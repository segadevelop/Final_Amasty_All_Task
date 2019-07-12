<?php

namespace Sega\Extension\Controller\Cart;

use Magento\Framework\App\Action\Context,
    Magento\Framework\View\Result\PageFactory,
    Magento\Framework\App\Action\Action,
    Sega\Extension\Model\PostFactory;

use Sega\Extension\Model\Product;

/**
 * Class Index
 * @package Sega\Extension\Controller\Index
 */
class Add extends Action
{
    /**
     * @var PageFactory
     */
    protected $pageFactory;

    /**
     * @var Product
     */
    protected $product;

    protected $postFactory;

    public function __construct(
        Context $context,
        PageFactory $pageFactory,
        Product $product,
        PostFactory $postFactory
    ) {
        $this->product = $product;
        $this->pageFactory = $pageFactory;
        $this->postFactory = $postFactory;
        return parent::__construct($context);
    }

    public function execute()
    {
//        $this->product->addProductBySku();
        $item = $this->postFactory->create();
        $this->product->setBeforeSetupAddToCart($item);
    }
}
