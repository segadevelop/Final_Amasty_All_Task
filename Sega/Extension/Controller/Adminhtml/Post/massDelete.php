<?php

namespace Sega\Extension\Controller\Adminhtml\Post;

use Magento\Backend\App\Action\Context,
    Magento\Ui\Component\MassAction\Filter,
    Magento\Backend\App\Action,
    Magento\Framework\Controller\ResultFactory;

use Sega\Extension\Model\PostFactory;

class MassDelete extends Action
{
    /**
     * @var Filter
     */
    protected $filter;
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;
    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Context $context,
        Filter $filter,
        PostFactory $collectionFactory
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context);
    }
    /**
     * Execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     */
    public function execute()
    {
        $ids = $this->getRequest()->getPost('selected');
        $collection = $this->collectionFactory->create();
        $count = 0;

        foreach ($ids as $id) {
            $collection->load($id);
            $collection->delete();
            $count++;
        }

        $this->messageManager->addSuccess(__('A total of %1 record(s) have been deleted.', $count));


        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }
}
