<?php

namespace Payin7\Mage2Payin7\Block;

class Payment extends \Payin7\Mage2Payin7\Block\Product {

    /**
     * Devuelve el estilo para el precio en la lista de productos.
     * @return string
     */
    public function getStyle() {
        $style = '';
        
        $width = $this->_scopeConfig->getValue('payment/mage2payin7/vistaPago/width');
        if($width) {
            $style .= 'width: '.(is_numeric($width) ? "$width%" : $width).';';
        }
        $align = $this->_scopeConfig->getValue('payment/mage2payin7/vistaPago/align');
        if($align) {
            $style .= "text-align: $align;";
        }
        $border = $this->_scopeConfig->getValue('payment/mage2payin7/vistaPago/borderWidth');
        if($border) {
            $style .= "border-width: {$border}px;";
        }
        
        return $style;
    }

    /**
     * Devuelve el precio para el plazo mas largo.
     * @return string
     */
    public function getDefaultPayin7Data() {
        $price = false;
        $months = 0;
        $tin = 0;
        $tae = 0;
        $commission =0;
        $commissions = $this->payin7->getCommissions(array($this->getTotal()));
        if(!empty($commissions[0]['installments'])) {
            foreach($commissions[0]['installments'] as $installment) {
                if(!empty($installment['default'])) {
                    $price = $installment['amount'];
                    $months = $installment['months'];
                    $commission = $installment['commission'];
                    if(isset($installment['tin'])) {
                        $tin = $installment['tin'];
                    }
                    if(isset($installment['tae'])) {
                        $tae = $installment['tae'];
                    }
                }
            }
        }

        return array('months' => $months, 'amount' => $price, 'tin' => $tin, 'tae' => $tae, 'commission' => $commission);
    }
    
    /**
     * Devuelve los installments.
     * @return string
     */
    public function getPayin7Data() {
        $payin7Data = array('attributeValues' => array(), 'price' => $this->getTotal(), 
            'commission' => $this->payin7->getCommissions(array($this->getTotal())));

        return $payin7Data;
    }
    
    /**
     * Devuelve el texto de importe financiado de la calculadora.
     * @return string
     */
    public function getTextoImporteFinanciado() {
        return $this->_scopeConfig->getValue('payment/mage2payin7/calculadora/textoImporteFinanciado');
    }

    /**
     * Devuelve el precio formateado.
     * @param float $price
     * @return string
     */
    public function formatPrice($price) {
        return $this->priceHelper->currency($price, true, false);
    }

}
