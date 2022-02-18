<?php

namespace Payin7\Mage2Payin7\Controller\Pay;

class Error extends \Magento\Framework\App\Action\Action {

    public function execute() {
        return $this->_redirect('checkout');
    }

}
