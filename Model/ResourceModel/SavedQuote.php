<?php

namespace Payin7\Mage2Payin7\Model\ResourceModel;

class SavedQuote extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb {
    
    public function _construct(){
        $this->_init("mage2payin7_savedQuote", "quote_id");
        $this->_isPkAutoIncrement = false;
    }

}
