<?php

namespace Sega\Extension\Model\ResourceModel\Post;

use \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Sega\Extension\Model\Post', 'Sega\Extension\Model\ResourceModel\Post');
    }

}