<?php

namespace Payin7\Mage2Payin7\Model\Config; 

class PosicionProductoSelect implements \Magento\Framework\Option\ArrayInterface {

    public function toOptionArray() {
        return [
            ['value' => 'before', 'label' => __('Bajo el precio')],
            ['value' => 'after', 'label' => __('Bajo los botones')],
            ['value' => 'layout', 'label' => __('Personalizada')]
        ];
    }

}
