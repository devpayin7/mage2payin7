<?php

namespace Payin7\Mage2Payin7\Model;

use Magento\Framework\Model\AbstractModel;
use Payin7\Mage2Payin7\Model\ResourceModel\Cache as ResourceModel;

class Cache extends AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'mage2payin7_cache_model';

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(ResourceModel::class);
    }

    public function getIdentities() {
        return [$this->_eventPrefix . '_' . $this->getId()];
    }
}
