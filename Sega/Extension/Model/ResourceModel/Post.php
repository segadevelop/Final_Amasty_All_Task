<?php

namespace Sega\Extension\Model\ResourceModel;

use \Magento\Framework\Model\ResourceModel\Db\AbstractDb,
    \Magento\Framework\Model\ResourceModel\Db\Context;

class Post extends AbstractDb
{

    public function __construct(
        Context $context
    )
    {
        parent::__construct($context);
    }

    protected function _construct()
    {
        $this->_init('sega_extension_post', 'post_id');
    }

}