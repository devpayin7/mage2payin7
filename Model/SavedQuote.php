<?php

namespace Payin7\Mage2Payin7\Model;

class SavedQuote extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface {
    const CACHE_TAG = 'payin7_mage2payin7_savedquote';
    
    public function _construct(){
		$this->_init("Payin7\Mage2Payin7\Model\ResourceModel\SavedQuote");
	}
    
    public function getIdentities() {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }
}
