<?php

namespace Payin7\Mage2Payin7\Block;

class Header extends \Magento\Framework\View\Element\Template {//TODO: el css de productNo y el de la calculadora compiten si tienen temas distintos (Probar).
    private $request, $assetRepository;
    
    public function __construct(
            \Magento\Framework\View\Element\Template\Context $context, 
            \Magento\Framework\App\Request\Http $request,
            \Magento\Framework\View\Asset\Repository $assetRepository,
            array $data = array()) {
        parent::__construct($context, $data);
        $this->request = $request;
        $this->assetRepository = $assetRepository;
    }
    
    /**
     * Devuelve el bloque de css.
     * @return string
     */
    public function getCssHtml() {
        $css = '';

        if($this->request->getActionName() == 'view') {
            if($this->request->getControllerName() == 'category') {
                //Lista de productos
                $css_basic = $this->assetRepository->createAsset('Payin7_Mage2Payin7::css/basic/categoryList.css');
                $css .= '<link href="'.$css_basic->getUrl().'" rel="stylesheet" type="text/css" />';
                $tema = $this->_scopeConfig->getValue('payment/mage2payin7/listado/tema');
                if($tema == 'manual') {
                    $css .= $this->getCategoryListManualCSS();
                }
                else {
                    $css_list = $this->assetRepository->createAsset("Payin7_Mage2Payin7::css/categoryList/$tema.css");
                    $css .= '<link href="'.$css_list->getUrl().'" rel="stylesheet" type="text/css" />';
                }
            }
            elseif($this->request->getControllerName() == 'product') {
                //Ficha de producto
                $css_basic = $this->assetRepository->createAsset('Payin7_Mage2Payin7::css/basic/product.css');
                $css .= '<link href="'.$css_basic->getUrl().'" rel="stylesheet" type="text/css" />';
                $css_basicNo = $this->assetRepository->createAsset('Payin7_Mage2Payin7::css/basic/productNo.css');
                $css .= '<link href="'.$css_basicNo->getUrl().'" rel="stylesheet" type="text/css" />';
                $temaProducto = $this->_scopeConfig->getValue('payment/mage2payin7/producto/tema');
                if($temaProducto == 'manual') {
                    $css .= $this->getProductManualCSS();
                }
                else {
                    $css_product = $this->assetRepository->createAsset("Payin7_Mage2Payin7::css/product/$temaProducto.css");
                    $css .= '<link href="'.$css_product->getUrl().'" rel="stylesheet" type="text/css" />';
                }
                $temaProductoNo = $this->_scopeConfig->getValue('payment/mage2payin7/noFinanciable/tema');
                $css_productNo = $this->assetRepository->createAsset("Payin7_Mage2Payin7::css/productNo/$temaProductoNo.css");
                $css .= '<link href="'.$css_productNo->getUrl().'" rel="stylesheet" type="text/css" />';
                $css .= '<style>.payin7_calculator { display: none; }</style>';
            }
        }
        elseif($this->request->getActionName() == 'index') {
            if($this->request->getControllerName() == 'cart') {
                //Carrito
                $css_basic = $this->assetRepository->createAsset('Payin7_Mage2Payin7::css/basic/paymentProcess.css');
                $css .= '<link href="'.$css_basic->getUrl().'" rel="stylesheet" type="text/css" />';
                $tema = $this->_scopeConfig->getValue('payment/mage2payin7/listado/tema');
                if($tema == 'manual') {
                    $css .= $this->getPaymentProcessManualCSS();
                }
                else {
                    $css_list = $this->assetRepository->createAsset("Payin7_Mage2Payin7::css/paymentProcess/$tema.css");
                    $css .= '<link href="'.$css_list->getUrl().'" rel="stylesheet" type="text/css" />';
                }
            }
            elseif($this->request->getFrontName() == 'checkout') {
                //Hacer pago
                $css_basic = $this->assetRepository->createAsset('Payin7_Mage2Payin7::css/basic/product.css');
                $css .= '<link href="'.$css_basic->getUrl().'" rel="stylesheet" type="text/css" />';
                $temaProducto = $this->_scopeConfig->getValue('payment/mage2payin7/producto/tema');
                if($temaProducto == 'manual') {
                    $css .= $this->getProductManualCSS();
                }
                else {
                    $css_product = $this->assetRepository->createAsset("Payin7_Mage2Payin7::css/product/$temaProducto.css");
                    $css .= '<link href="'.$css_product->getUrl().'" rel="stylesheet" type="text/css" />';
                }
            }
        }
        
        return $css;
    }
    
    /**
     * Devuelve el css para el producto en modo manual.
     * @return string
     */
    private function getProductManualCSS() {
        $text = '<style>' . PHP_EOL;

        $text .= '.calculator_left {' . PHP_EOL;
        $text .= ' float:left;';
        $text .= ' width:50%;';
        $text .= '}' . PHP_EOL;

        $text .= '.calculator_right {' . PHP_EOL;
        $text .= ' float:right;';
        $text .= ' width:50%;';
        $text .= '}' . PHP_EOL;


        $text .= '.payin_calculator {' . PHP_EOL;
        $text .= '   background: ' . $this->_scopeConfig->getValue('payment/mage2payin7/calculadora/fondo') . ';' . PHP_EOL;
        $text .= '   display: block;' . PHP_EOL;
        $text .= '   overflow: auto;' . PHP_EOL;
        $text .= 'border: none !Important;' . PHP_EOL;
        $text .= '}' . PHP_EOL;

        $text .= '.payin_calculator .calc-body .btn.btn-minus {' . PHP_EOL;
        $text .= 'background-image: url("'.$this->assetRepository->createAsset("Payin7_Mage2Payin7::images/menos-up.png")->getUrl().'");' . PHP_EOL;
        $text .= 'background-repeat: no-repeat;' . PHP_EOL;
        $text .= 'background-size: contain;' . PHP_EOL;
        $text .= '}' . PHP_EOL;

        $text .= '.payin_calculator .calc-body .btn.btn-plus {' . PHP_EOL;
        $text .= 'background-image: url("'.$this->assetRepository->createAsset("Payin7_Mage2Payin7::images/mas-up.png")->getUrl().'");' . PHP_EOL;
        $text .= 'background-repeat: no-repeat;' . PHP_EOL;
        $text .= 'background-size: contain;' . PHP_EOL;
        $text .= '}' . PHP_EOL;

        $text .= '.payin_calculator .calc-body .btn.btn-minus.mousedown {' . PHP_EOL;
        $text .= 'background-image: url("'.$this->assetRepository->createAsset("Payin7_Mage2Payin7::images/menos-down.png")->getUrl().'");' . PHP_EOL;
        $text .= '}' . PHP_EOL;

        $text .= '.payin_calculator .calc-body .btn.btn-plus.mousedown {' . PHP_EOL;
        $text .= 'background-image: url("'.$this->assetRepository->createAsset("Payin7_Mage2Payin7::images/mas-down.png")->getUrl().'");' . PHP_EOL;
        $text .= '}' . PHP_EOL;

        $text .= '.installments-count {' . PHP_EOL;
        $text .= 'background-image: url("'.$this->assetRepository->createAsset("Payin7_Mage2Payin7::images/cuotas.png")->getUrl().'");' . PHP_EOL;
        $text .= 'background-repeat: no-repeat;' . PHP_EOL;
        $text .= 'background-size: cover;' . PHP_EOL;
        $text .= 'border: none !Important;' . PHP_EOL;
        $text .= 'background-color: transparent;' . PHP_EOL;
        $text .= '}' . PHP_EOL;

        $text .= '.payin_calculator .finance-title {' . PHP_EOL;
        $text .= 'color:' . $this->_scopeConfig->getValue('payment/mage2payin7/calculadora/titulo') . ';' . PHP_EOL;
        $text .= '}' . PHP_EOL;

        $text .= '.payin_calculator .finance-subtitle {' . PHP_EOL;
        $text .= 'color:' . $this->_scopeConfig->getValue('payment/mage2payin7/calculadora/subtitulo') . ';' . PHP_EOL;
        $text .= '}' . PHP_EOL;

        $text .= '.payin_calculator .finance-footer {' . PHP_EOL;
        $text .= 'color:' . $this->_scopeConfig->getValue('payment/mage2payin7/calculadora/pie') . ';' . PHP_EOL;
        $text .= '}' . PHP_EOL;

        $text .= '.payin_calculator .finance-htmlfooter {' . PHP_EOL;
        $text .= 'color:' . $this->_scopeConfig->getValue('payment/mage2payin7/calculadora/pieHtml') . ';' . PHP_EOL;
        $text .= 'clear:both;' . PHP_EOL;

        $text .= '}' . PHP_EOL;


        $text .= '.payin_calculator .installments-sub {' . PHP_EOL;
        $text .= 'color:' . $this->_scopeConfig->getValue('payment/mage2payin7/calculadora/cuotas') . ';' . PHP_EOL;
        $text .= '}' . PHP_EOL;

        $text .= '.payin_calculator .calc-results, ' . PHP_EOL;
        $text .= '.payin_calculator .calc-results span' . PHP_EOL;
        $text .= '{' . PHP_EOL;
        $text .= 'color:' . $this->_scopeConfig->getValue('payment/mage2payin7/calculadora/importeMes') . ';' . PHP_EOL;
        $text .= '}' . PHP_EOL;
        $text .= '.payin_calculator.vertical .calc-body .btn-minus::after ,' . PHP_EOL;
        $text .= '.payin_calculator.vertical .calc-body .btn-plus::after {' . PHP_EOL;
        $text .= 'content:"" !Important;' . PHP_EOL;
        $text .= '}' . PHP_EOL;
        $text .= '</style>' . PHP_EOL;

        return $text;
    }

    /**
     * Devuelve el css para la lista de productos en modo manual.
     * @return string
     */
    private function getCategoryListManualCSS() {
        $text = '<style>' . PHP_EOL;
        $text .= ' .payin_calculator.categoryList { ' . PHP_EOL;
        $text .= ' background-color:' . $this->_scopeConfig->getValue('payment/mage2payin7/calculadora/fondo') . '; ' . PHP_EOL;
        $text .= ' color: #000; ' . PHP_EOL;
        $text .= ' border-color: #000; ' . PHP_EOL;
        $text .= ' }' . PHP_EOL;

        $text .= ' .payin_calculator.categoryList p span.payinPrice {' . PHP_EOL;
        $text .= ' color: ' . $this->_scopeConfig->getValue('payment/mage2payin7/calculadora/importeMes') . ' !Important;' . PHP_EOL;
        $text .= ' }' . PHP_EOL;

        $text .= ' .payin_calculator.categoryList p, .payin_calculator.categoryList p span  {' . PHP_EOL;
        $text .= ' color: ' . $this->_scopeConfig->getValue('payment/mage2payin7/calculadora/importeMes') . ' !Important;' . PHP_EOL;
        $text .= ' font-weight: bold;' . PHP_EOL;
        $text .= ' }' . PHP_EOL;
        $text .= '</style>' . PHP_EOL;
        
        return $text;
    }

    /**
     * Devuelve el css para el pago en modo manual.
     * @return string
     */
    private function getPaymentProcessManualCSS() {
        $text = '<style>' . PHP_EOL;
        $text .= ' .payin_calculator.productNo { ' . PHP_EOL;
        $text .= ' background-color:' . $this->_scopeConfig->getValue('payment/mage2payin7/calculadora/fondo') . '; ' . PHP_EOL;
        $text .= ' color: #000; ' . PHP_EOL;
        $text .= ' border-color: #000; ' . PHP_EOL;
        $text .= ' }' . PHP_EOL;

        $text .= ' .payin_calculator.productNo p span.payinPrice {' . PHP_EOL;
        $text .= ' color: ' . $this->_scopeConfig->getValue('payment/mage2payin7/calculadora/importeMes') . ' !Important;' . PHP_EOL;
        $text .= ' }' . PHP_EOL;

        $text .= '.payin_calculator .finance-title {' . PHP_EOL;
        $text .= 'color:' . $this->_scopeConfig->getValue('payment/mage2payin7/calculadora/titulo') . ';' . PHP_EOL;
        $text .= '}' . PHP_EOL;

        $text .= '.payin_calculator .finance-subtitle {' . PHP_EOL;
        $text .= 'color:' . $this->_scopeConfig->getValue('payment/mage2payin7/calculadora/subtitulo') . ';' . PHP_EOL;
        $text .= '}' . PHP_EOL;
        $text .= '</style>' . PHP_EOL;

        return $text;
    }
    
    /**
     * Devuelve el bloque de js.
     * @return string
     */
    public function getJsHtml() {
        $js = '';
        
        if(($this->request->getActionName() == 'view' && $this->request->getControllerName() == 'product') || 
                ($this->request->getActionName() == 'index' && $this->request->getFrontName() == 'checkout')) {
            $showTinTae = $this->_scopeConfig->getValue('payment/mage2payin7/calculadora/mostrarTinTae');
            $js .= '
                <script type="text/javascript">
                    var positionParent = "'.$this->_scopeConfig->getValue('payment/mage2payin7/calculadora/selectorPosicionCalculadora').'";
                    var position = "'.$this->_scopeConfig->getValue('payment/mage2payin7/calculadora/moverCalculadora').'";
                    var showTinTae = '.($showTinTae ? 'true' : 'false').';
                </script>';
            $jsPath = $this->assetRepository->createAsset('Payin7_Mage2Payin7::js/payin7.js');
            $js .= '<script type="text/javascript" src="'.$jsPath->getUrl().'"></script>';
        }
        
        return $js;
    }
}
