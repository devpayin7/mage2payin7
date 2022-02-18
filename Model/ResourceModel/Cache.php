<?php

namespace Payin7\Mage2Payin7\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Cache extends AbstractDb
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'mage2payin7_cache_resource_model';

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init('mage2payin7_cache', 'id');
        $this->_isPkAutoIncrement = false;
    }
}
