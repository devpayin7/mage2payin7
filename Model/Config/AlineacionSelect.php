<?php

namespace Payin7\Mage2Payin7\Model\Config; 

class AlineacionSelect implements \Magento\Framework\Option\ArrayInterface {

    public function toOptionArray() {
        return [
            ['value' => 'left', 'label' => __('Izquierda')],
            ['value' => 'center', 'label' => __('Centrado')],
            ['value' => 'right', 'label' => __('Derecha')]
        ];
    }

}
