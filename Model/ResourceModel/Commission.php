<?php

namespace Payin7\Mage2Payin7\Model\ResourceModel;

class Commission extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb {
    
    public function _construct(){
        $this->_init("mage2payin7_commission", "amount");
        $this->_isPkAutoIncrement = false;
    }

}
