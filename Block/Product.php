<?php

namespace Payin7\Mage2Payin7\Block;

class Product extends \Magento\Framework\View\Element\Template {
    protected $priceHelper, $payin7, $repository;
    
    public function __construct(\Magento\Framework\View\Element\Template\Context $context, 
            \Magento\Framework\Pricing\Helper\Data $priceHelper, 
            \Payin7\Mage2Payin7\Helper\Payin7 $payin7,
            \Magento\Catalog\Model\ProductRepository $repository,
            array $data = array()) {
        parent::__construct($context, $data);
        $this->priceHelper = $priceHelper;
        $this->payin7 = $payin7;
        $this->repository = $repository;
    }
    
    /**
     * Devuelve el estilo para el precio en la lista de productos.
     * @return string
     */
    public function getStyle() {
        $style = '';
        
        $width = $this->_scopeConfig->getValue('payment/mage2payin7/producto/width');
        if($width) {
            $style .= 'width: '.(is_numeric($width) ? "$width%" : $width).';';
        }
        $align = $this->_scopeConfig->getValue('payment/mage2payin7/producto/align');
        if($align) {
            $style .= "text-align: $align;";
        }
        $border = $this->_scopeConfig->getValue('payment/mage2payin7/producto/borderWidth');
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
    
    /**
     * Devuelve los installments.
     * @return string
     */
    public function getPayin7Data() {
        $payin7Data = array();
        
        if($this->getProduct()->getTypeId() == 'configurable') {
            $attributeValues = array();
            $options = $this->getProduct()->getTypeInstance()->getConfigurableOptions($this->getProduct());
            foreach($options as $attributes){
                foreach($attributes as $attribute){
                    $attributeValues[$attribute['sku']][] = $attribute['value_index'];
                }
            }

            $prices = array();
            foreach($attributeValues as $sku => $attributes){
                $product = $this->repository->get($sku);
                $payin7Data[] = array('attributeValues' => $attributes, 'price' => $product->getFinalPrice());
                $prices[] = $product->getFinalPrice();
            }

            $commissions = $this->payin7->getCommissions($prices);
            foreach($commissions as $commission) {
                $this->insertCommission($payin7Data, $commission);
            }
        }
        else {
            $payin7Data = array('attributeValues' => array(), 'price' => $this->getProduct()->getFinalPrice(), 
                'commission' => $this->payin7->getCommissions(array($this->getProduct()->getFinalPrice())));
        }

        return $payin7Data;
    }
    
    /**
     * Introduce los datos de Payin7 en el array.
     * @param array $payin7Data
     * @param array $commission
     */
    private function insertCommission(&$payin7Data, $commission) {
        foreach($payin7Data as &$data) {
            if($data['price'] == $commission['amount']) {
                $data['commission'] = $commission;
            }
        }
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
     * Devuelve el tipo de visualizacion.
     * @return string
     */
    public function getClass() {
        return $this->_scopeConfig->getValue('payment/mage2payin7/producto/tipoVisualizacion');
    }
    
    /**
     * Devuelve el titulo de la calculadora.
     * @return string
     */
    public function getTitulo() {
        return $this->_scopeConfig->getValue('payment/mage2payin7/calculadora/textoTitulo');
    }
    
    /**
     * Devuelve el subtitulo de la calculadora.
     * @return string
     */
    public function getSubtitulo() {
        return $this->_scopeConfig->getValue('payment/mage2payin7/calculadora/textoSubtitulo');
    }
    
    /**
     * Devuelve el texto del importe por mes de la calculadora.
     * @return string
     */
    public function getTextoImporteMes() {
        return $this->_scopeConfig->getValue('payment/mage2payin7/calculadora/textoImporteMes');
    }
    
    /**
     * Devuelve el texto de las cuotas de la calculadora.
     * @return string
     */
    public function getTextoCuotas() {
        return $this->_scopeConfig->getValue('payment/mage2payin7/calculadora/textoCuotas');
    }
    
    /**
     * Devuelve el texto del pie de la calculadora.
     * @return string
     */
    public function getTextoPie() {
        return $this->_scopeConfig->getValue('payment/mage2payin7/calculadora/textoPie');
    }
    
    /**
     * Devuelve el html del pie de la calculadora.
     * @return string
     */
    public function getTextoPieHtml() {
        return $this->_scopeConfig->getValue('payment/mage2payin7/calculadora/textoPieHtml');
    }
    
    /**
     * Devuelve el coste minimo financiable.
     * @return float
     */
    public function getCosteMinimo() {
        return $this->_scopeConfig->getValue('payment/mage2payin7/general/minimum_cost');
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
