<?php

namespace Sega\Extension\Controller\Adminhtml\Post;

use \Magento\Backend\App\Action,
    \Magento\Backend\App\Action\Context,
    \Magento\Framework\View\Result\PageFactory;

class AddRule extends Action
{
    protected $resultPageFactory = false;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    public function execute()
    {
    }


}