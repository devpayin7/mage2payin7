<?php

namespace Payin7\Mage2Payin7\Model\Config; 

class CalculadoraSelect implements \Magento\Framework\Option\ArrayInterface {

    public function toOptionArray() {
        return [
            ['value' => 1, 'label' => __('Principio')],
            ['value' => 2, 'label' => __('Final')]
        ];
    }

}
