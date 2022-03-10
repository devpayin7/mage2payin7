<?php

namespace Payin7\Mage2Payin7\Controller\Pay;

class Cancel extends \Magento\Framework\App\Action\Action implements \Magento\Framework\App\Action\HttpPostActionInterface{

    const COOKIE_DURATION = 10 * 365 * 24 * 60 * 60;
    const COOKIE_NAME = 'payin7';
    
    protected $_pageFactory, $_cookieManager, $_cookieMetadataFactory, $savedQuoteFactory, $checkoutSession;

    public function __construct(
            \Magento\Framework\App\Action\Context $context,
            \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
            \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory,
            \Payin7\Mage2Payin7\Model\SavedQuoteFactory $savedQuoteFactory,
            \Magento\Checkout\Model\Session $checkoutSession) {
        $this->_cookieManager = $cookieManager;
        $this->_cookieMetadataFactory = $cookieMetadataFactory;
        $this->savedQuoteFactory = $savedQuoteFactory;
        $this->checkoutSession = $checkoutSession;
        parent::__construct($context);
    }

    public function execute(): \Magento\Framework\Controller\ResultInterface|ResponseInterface
    {
        if($this->_request->getParam('order_state') == 'rejected') {
            $metadata = $this->_cookieMetadataFactory
                ->createPublicCookieMetadata()
                ->setDuration(self::COOKIE_DURATION);
            $this->_cookieManager->setPublicCookie(
                self::COOKIE_NAME,
                ':(',
                $metadata
            );
        }
        
        //Cambiamos el temp id para evitar bloqueos en Payin7
        $savedQuote = $this->savedQuoteFactory->create();
        $savedQuote->load($this->checkoutSession->getQuote()->getId());
        if(!$savedQuote->isEmpty()) {
            $payin7_order_id = $this->checkoutSession->getQuote()->getId() . uniqid('_');
            $savedQuote->setTempId($payin7_order_id);
            $savedQuote->save();
        }
        
        return $this->_redirect('checkout');
    }

}
