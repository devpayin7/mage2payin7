<?php

namespace Payin7\Mage2Payin7\Block;

class BaseBlock extends \Magento\Framework\View\Element\Template {
    private $priceHelper, $payin7;
    
    public function __construct(\Magento\Framework\View\Element\Template\Context $context, 
            \Magento\Framework\Pricing\Helper\Data $priceHelper, 
            \Payin7\Mage2Payin7\Helper\Payin7 $payin7,
            array $data = array()) {
        parent::__construct($context, $data);
        $this->priceHelper = $priceHelper;
        $this->payin7 = $payin7;
    }
    
    /**
     * Devuelve el precio para el plazo mas largo.
     * @return string
     */
    public function getPrice() {
        $price = false;
        $months = 0;
        
        $commissions = $this->payin7->getCommissions(array($this->getProductPrice()));
        if(!empty($commissions[0]['installments'])) {
            foreach($commissions[0]['installments'] as $installment) {
                if($installment['months'] > $months) {
                    $price = $installment['amount'];
                    $months = $installment['months'];
                }
            }
        }

        return ($price ? $this->priceHelper->currency($price, true, false) : false);
    }
    
    /**
     * Devuelve el precio para el plazo mas largo.
     * @return string
     */
    public function getMaxMonthsPayin7Data() {
        $price = false;
        $months = 0;
        $tin = 0;
        $tae = 0;
        $commission =0;
        $commissions = $this->payin7->getCommissions(array($this->getProductPrice()));
        if(!empty($commissions[0]['installments'])) {
            foreach($commissions[0]['installments'] as $installment) {
                if($installment['months'] > $months) {
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
     * Devuelve el precio para el plazo mas largo.
     * @return string
     */
    public function getDefaultPayin7Data() {
        $price = false;
        $months = 0;
        $tin = 0;
        $tae = 0;
        $commission =0;
        $commissions = $this->payin7->getCommissions(array($this->getProduct()->getPriceInfo()->getPrice('final_price')->getAmount()->getValue()));
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
    public function getCommissions($amounts) {
        return $this->payin7->getCommissions($amounts);
    }

    /**
     * Devuelve el precio formateado.
     * @param float $price
     * @return string
     */
    public function formatPrice($price) {
        return $this->priceHelper->currency($price, true, false);
    }
    
    /**
     * Indica si se debe mostrar la informacion de Tin y Tae.
     * @return boolean
     */
    public function showTinTae() {
        return (boolean)$this->_scopeConfig->getValue('payment/mage2payin7/calculadora/mostrarTinTae');
    }
    
    /**
     * Devuelve el texto de importe financiado de la calculadora.
     * @return string
     */
    public function getTextoImporteFinanciado() {
        return $this->_scopeConfig->getValue('payment/mage2payin7/calculadora/textoImporteFinanciado');
    }

}
