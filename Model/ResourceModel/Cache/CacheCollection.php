<?php

namespace Payin7\Mage2Payin7\Model\ResourceModel\Cache;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Payin7\Mage2Payin7\Model\Cache as Model;
use Payin7\Mage2Payin7\Model\ResourceModel\Cache as ResourceModel;

class CacheCollection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'mage2payin7_cache_collection';

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(Model::class, ResourceModel::class);
    }
}
