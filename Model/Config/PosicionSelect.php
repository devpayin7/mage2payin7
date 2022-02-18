<?php

namespace Payin7\Mage2Payin7\Model\Config; 

class PosicionSelect implements \Magento\Framework\Option\ArrayInterface {

    public function toOptionArray() {
        return [
            ['value' => 'after_price', 'label' => __('Debajo')],
            ['value' => 'before_price', 'label' => __('Encima')]
        ];
    }

}
