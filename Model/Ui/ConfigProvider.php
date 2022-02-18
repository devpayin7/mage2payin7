<?php

namespace Payin7\Mage2Payin7\Model\Ui;

use Magento\Checkout\Model\ConfigProviderInterface;
use Payin7\Mage2Payin7\Gateway\Http\Client\ClientMock;

/**
 * Class ConfigProvider
 */
final class ConfigProvider implements ConfigProviderInterface
{
    const CODE = 'mage2payin7';
    
    private $scopeConfig, $payin7, $checkoutSession, $localeResolver, $shippingMethod, $savedQuoteFactory, $layout;
    
    public function __construct(\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, \Payin7\Mage2Payin7\Helper\Payin7 $payin7, 
            \Magento\Checkout\Model\Session $checkoutSession, \Magento\Framework\Locale\Resolver $localeResolver, 
            \Magento\Quote\Api\Data\ShippingMethodInterface $shippingMethod,
            \Payin7\Mage2Payin7\Model\SavedQuoteFactory $savedQuoteFactory,
            \Magento\Framework\View\LayoutInterface $layout) {
        $this->scopeConfig = $scopeConfig;
        $this->payin7 = $payin7;
        $this->checkoutSession = $checkoutSession;
        $this->localeResolver = $localeResolver;
        $this->shippingMethod = $shippingMethod;
        $this->savedQuoteFactory = $savedQuoteFactory;
        $this->layout = $layout;
    }

    /**
     * Retrieve assoc array of checkout configuration
     *
     * @return array
     */
    public function getConfig()
    {
        $savedQuote = $this->savedQuoteFactory->create();
        $savedQuote->load($this->checkoutSession->getQuote()->getId());
        if($savedQuote->isObjectNew()) {
            $payin7_order_id = $this->checkoutSession->getQuote()->getId() . uniqid('_');
            $savedQuote->setQuoteId($this->checkoutSession->getQuote()->getId());
            $savedQuote->setTempId($payin7_order_id);
            $savedQuote->save();
        }
        $this->checkoutSession->getQuote()->setTotalsCollectedFlag(false)->collectTotals();
        $block = $this->layout->createBlock('Payin7\Mage2Payin7\Block\Payment')
                                ->setTemplate('Payin7_Mage2Payin7::product_'.$this->scopeConfig->getValue('payment/mage2payin7/producto/tipoVisualizacion').'.phtml')
                                ->addData(array('total' => $this->checkoutSession->getQuote()->getGrandTotal()))
                                ->toHtml();
        
        return [
            'payment' => [
                self::CODE => [
                    'integration_id' => $this->scopeConfig->getValue('payment/mage2payin7/general/integration_id'),
                    'sandbox' => $this->scopeConfig->getValue('payment/mage2payin7/general/sandbox'),
                    'signature' => $this->payin7->createSignature($savedQuote->getTempId(), $this->checkoutSession->getQuote()->getGrandTotal()),
                    'locale' => strstr($this->localeResolver->getLocale(), '_', true),
                    'payin7_order_id' => $savedQuote->getTempId(),
                    'payin7_url' => $this->scopeConfig->getValue('payment/mage2payin7/general/payin7_url'),
                    'calculator_block' => $block,
                    'payin7_quote_id' => $this->checkoutSession->getQuote()->getId(),
                    'despedida' => $this->scopeConfig->getValue('payment/mage2payin7/vistaPago/despedida'),
                    'textoDespedida' => $this->scopeConfig->getValue('payment/mage2payin7/vistaPago/textoDespedida'),
                    'tiempoDespedida' => (int)$this->scopeConfig->getValue('payment/mage2payin7/vistaPago/tiempoDespedida')
                ]
            ]
        ];
    }
}
