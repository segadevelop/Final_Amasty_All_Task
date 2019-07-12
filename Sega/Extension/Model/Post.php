<?php

namespace Sega\Extension\Model;

use \Magento\Framework\Model\AbstractModel,
    \Magento\Framework\DataObject\IdentityInterface;

class Post extends AbstractModel implements IdentityInterface
{
    const CACHE_TAG = 'sega_extension_post';

    protected function _construct()
    {
        $this->_init('Sega\Extension\Model\ResourceModel\Post');
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }
    
}

