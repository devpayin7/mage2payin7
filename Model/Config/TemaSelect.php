<?php

namespace Payin7\Mage2Payin7\Model\Config; 

class TemaSelect implements \Magento\Framework\Option\ArrayInterface {

    public function toOptionArray() {
        return [
            ['value' => 'manual', 'label' => __('Sin tema')],
            ['value' => 'azul', 'label' => __('Azul')],
            ['value' => 'azul_blanco', 'label' => __('Azul / Blanco')],
            ['value' => 'azul_gris', 'label' => __('Azul / Gris')],
            ['value' => 'gris', 'label' => __('Gris')],
            ['value' => 'neutro', 'label' => __('Neutro')]
        ];
    }

}
