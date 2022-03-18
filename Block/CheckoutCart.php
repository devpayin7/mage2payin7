<?php

namespace Payin7\Mage2Payin7\Block;

class CheckoutCart extends \Magento\Framework\View\Element\Template {
    private $session, $payin7, $priceHelper, $cookieManager;
    
    const COOKIE_NAME = 'payin7';
    
    public function __construct(\Magento\Framework\View\Element\Template\Context $context, 
            \Magento\Checkout\Model\Session $session, 
            \Magento\Framework\Pricing\Helper\Data $priceHelper, 
            \Payin7\Mage2Payin7\Helper\Payin7 $payin7,
            \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
            array $data = array()) {
        parent::__construct($context, $data);
        $this->session = $session;
        $this->payin7 = $payin7;
        $this->priceHelper = $priceHelper;
        $this->cookieManager = $cookieManager;
    }
    
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
    public function getPrice() {
        $price = false;
        $months = 0;
        $commissions = $this->payin7->getCommissions(array($this->session->getQuote()->getGrandTotal()));
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
    
    protected function _toHtml() {
        $configPos = $this->_scopeConfig->getValue('payment/mage2payin7/vistaPago/posicion');
        if($configPos == $this->getPos() && $this->cookieManager->getCookie(self::COOKIE_NAME) != ':(') {
            return parent::_toHtml();
        }
        else {
            return '';
        }
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
        $commissions = $this->payin7->getCommissions(array($this->session->getQuote()->getGrandTotal()));
        if(!empty($commissions[0]['installments'])) {
            foreach($commissions[0]['installments'] as $installment) {
                if($installment['months'] > $months) {
                    $price = $installment['amount'];
                    $months = $installment['months'];

                    $commission = 0;

                    if (isset($installment['commission'])) {
                        $commission = $installment['commission'];
                    }

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
