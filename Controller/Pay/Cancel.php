<?php

namespace Payin7\Mage2Payin7\Controller\Pay;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Request\InvalidRequestException;

class Cancel extends \Magento\Framework\App\Action\Action implements CsrfAwareActionInterface {

    const COOKIE_DURATION = 10 * 365 * 24 * 60 * 60;
    const COOKIE_NAME = 'payin7';
    
    protected $_pageFactory, $_cookieManager, $_cookieMetadataFactory, $savedQuoteFactory, $checkoutSession;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Data\Form\FormKey $formKey,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory,
        \Payin7\Mage2Payin7\Model\SavedQuoteFactory $savedQuoteFactory,
        \Magento\Checkout\Model\Session $checkoutSession
    ) {
            $this->request = $request;
            $this->formKey = $formKey;
            $this->request->setParam('form_key', $this->formKey->getFormKey());
            $this->_cookieManager = $cookieManager;
            $this->_cookieMetadataFactory = $cookieMetadataFactory;
            $this->savedQuoteFactory = $savedQuoteFactory;
            $this->checkoutSession = $checkoutSession;

            if (interface_exists("\Magento\Framework\App\CsrfAwareActionInterface")) {
                $request = $this->getRequest();
                if ($request instanceof HttpRequest && $request->isPost() && empty($request->getParam('form_key'))) {
                    $formKey = $this->_objectManager->get(\Magento\Framework\Data\Form\FormKey::class);
                    $request->setParam('form_key', $formKey->getFormKey());
                }
            }
            
            parent::__construct($context);
    }

    public function execute()
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
        
        $order = $this->checkoutSession->getLastRealOrder();
        if ($order->getId() && $order->getState() != Order::STATE_CANCELED) {
            $order->registerCancellation('Payin7 cancelled')->save();
        }

        $this->checkoutSession->restoreQuote();

        $this->checkoutSession->getQuote()->setIsActive(false);
        $this->checkoutSession->getQuote()->save();
        //Cambiamos el temp id para evitar bloqueos en Payin7
        // $savedQuote = $this->savedQuoteFactory->create();
        // $savedQuote->load($this->checkoutSession->getQuote()->getId());
        // if(!$savedQuote->isEmpty()) {
        //     $payin7_order_id = $this->checkoutSession->getQuote()->getId() . uniqid('_');
        //     $savedQuote->setTempId($payin7_order_id);
        //     $savedQuote->save();
        // }
        
        $this->_redirect('checkout/cart');
    }

    /**
     * @inheritDoc
     */
    public function createCsrfValidationException(RequestInterface $request): ?InvalidRequestException {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function validateForCsrf(RequestInterface $request): ?bool {
        return true;
    }
}
