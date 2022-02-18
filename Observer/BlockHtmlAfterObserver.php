<?php

namespace Payin7\Mage2Payin7\Observer;

class BlockHtmlAfterObserver implements \Magento\Framework\Event\ObserverInterface {
    
    private $layout, $scopeConfig, $cookieManager;
    
    const COOKIE_NAME = 'payin7';
    
    public function __construct(
            \Magento\Framework\View\LayoutInterface $layout,
            \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
            \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager
    ) {
        $this->layout = $layout;
        $this->scopeConfig = $scopeConfig;
        $this->cookieManager = $cookieManager;
    }
    
    public function execute(\Magento\Framework\Event\Observer $observer) {
        if($this->cookieManager->getCookie(self::COOKIE_NAME) != ':(') {
            if($observer->getData('block')->getData('zone') === 'item_list') {
                //Listado de productos
                if($this->scopeConfig->getValue('payment/mage2payin7/listado/activo')) {
                    $price = $observer->getData('block')->getDisplayValue();
                    if(get_class($observer->getData('block')) === 'Magento\Framework\Pricing\Render\Amount' && 
                            $observer->getData('block')->getPriceType() == 'finalPrice' && 
                            $price >= $this->scopeConfig->getValue('payment/mage2payin7/general/minimum_cost') &&
                                $price <= $this->scopeConfig->getValue('payment/mage2payin7/general/maximum_cost')) {
                        $block = $this->layout->createBlock('Payin7\Mage2Payin7\Block\CategoryList')
                                ->setTemplate('Payin7_Mage2Payin7::category-list.phtml')
                                ->addData(array('product_price' => $observer->getData('block')->getDisplayValue()))
                                ->toHtml();
                        if($this->scopeConfig->getValue('payment/mage2payin7/listado/posicion') == 'before_price') {
                            $observer->getData('transport')['html'] = $block.$observer->getData('transport')['html'];
                        }
                        else {
                            $observer->getData('transport')['html'] .= $block;
                        }
                    }
                }
            }
            else {
                //Ficha del producto
                if($observer->getData('block')->getNameInLayout() === 'product.info.addtocart' || $observer->getData('block')->getNameInLayout() === 'product.info.addtocart.additional') {
                    $product = $observer->getData('block')->getProduct();

                    if($product->getTypeId() == 'configurable') {
                        //Producto configurable
                        if($this->scopeConfig->getValue('payment/mage2payin7/producto/activo')) {
                            $block = $this->layout->createBlock('Payin7\Mage2Payin7\Block\Product')
                                    ->setTemplate('Payin7_Mage2Payin7::product_'.$this->scopeConfig->getValue('payment/mage2payin7/producto/tipoVisualizacion').'.phtml')
                                    ->addData(array('product' => $product))
                                    ->toHtml();
                        }
                        if($this->scopeConfig->getValue('payment/mage2payin7/noFinanciable/activo')) {
                            $block .= $this->layout->createBlock('Payin7\Mage2Payin7\Block\Product')
                                    ->setTemplate('Payin7_Mage2Payin7::productNo.phtml')
                                    ->addData(array('product' => $product))
                                    ->toHtml();
                        }
                    }
                    else {
                        //Producto simple
                        if($product->getFinalPrice() >= $this->scopeConfig->getValue('payment/mage2payin7/general/minimum_cost') &&
                                $product->getFinalPrice() <= $this->scopeConfig->getValue('payment/mage2payin7/general/maximum_cost')) {
                            //Dentro de rango
                            if($this->scopeConfig->getValue('payment/mage2payin7/producto/activo')) {
                                $block = $this->layout->createBlock('Payin7\Mage2Payin7\Block\Product')
                                    ->setTemplate('Payin7_Mage2Payin7::product_'.$this->scopeConfig->getValue('payment/mage2payin7/producto/tipoVisualizacion').'.phtml')
                                    ->addData(array('product' => $product))
                                    ->toHtml();
                            }
                        }
                        else {
                            //Fuera de rango
                            if($this->scopeConfig->getValue('payment/mage2payin7/noFinanciable/activo')) {
                                $block = $this->layout->createBlock('Payin7\Mage2Payin7\Block\Product')
                                    ->setTemplate('Payin7_Mage2Payin7::productNo.phtml')
                                    ->addData(array('product' => $product))
                                    ->toHtml();
                            }
                        }
                    }

                    if(!empty($block)) {
                        if($this->scopeConfig->getValue('payment/mage2payin7/producto/posicion') == 'before') {
                            $observer->getData('transport')['html'] = $block.$observer->getData('transport')['html'];
                        }
                        elseif($this->scopeConfig->getValue('payment/mage2payin7/producto/posicion') == 'after') {
                            $observer->getData('transport')['html'] .= $block;
                        }
                    }
                }
            }
        }
    }

}
