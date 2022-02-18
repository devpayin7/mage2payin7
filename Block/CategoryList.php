<?php

namespace Payin7\Mage2Payin7\Block;

class CategoryList extends BaseBlock {

    public function __construct(\Magento\Framework\View\Element\Template\Context $context, 
            \Magento\Framework\Pricing\Helper\Data $priceHelper, 
            \Payin7\Mage2Payin7\Helper\Payin7 $payin7,
            array $data = array()) {
        parent::__construct($context, $priceHelper,$payin7,$data);
    }
    
    /**
     * Devuelve el estilo para el precio en la lista de productos.
     * @return string
     */
    public function getStyle() {
        $style = '';
        
        $width = $this->_scopeConfig->getValue('payment/mage2payin7/listado/width');
        if($width) {
            $style .= 'width: '.(is_numeric($width) ? "$width%" : $width).';';
        }
        $align = $this->_scopeConfig->getValue('payment/mage2payin7/listado/align');
        if($align) {
            $style .= "text-align: $align;";
        }
        $border = $this->_scopeConfig->getValue('payment/mage2payin7/listado/borderWidth');
        if($border) {
            $style .= "border-width: {$border}px;";
        }
        
        return $style;
    }

}
