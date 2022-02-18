<?php

namespace Payin7\Mage2Payin7\Model\Config; 

class TipoVisualizacionSelect implements \Magento\Framework\Option\ArrayInterface {

    public function toOptionArray() {
        return [
            ['value' => 'horizontal', 'label' => __('Horizontal')],
            ['value' => 'vertical', 'label' => __('Vertical')],
            ['value' => 'drop_small', 'label' => __('Dropdown small')],
            ['value' => 'drop_large', 'label' => __('Dropdown large')],
            ['value' => 'horizontal_logo', 'label' => __('Horizontal logo')]
        ];
    }

}
