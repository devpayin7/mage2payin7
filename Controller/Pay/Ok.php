<?php

namespace Payin7\Mage2Payin7\Controller\Pay;

use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Checkout\Model\Cart as CustomerCart;
use Magento\Framework\Session\SessionManagerInterface;

class Ok extends \Magento\Framework\App\Action\Action implements CsrfAwareActionInterface
{
    protected $_pageFactory;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Checkout\Model\Cart
     */
    protected $cart;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        SessionManagerInterface $checkoutSession,
        CustomerCart $cart
    ) {
        // $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        // $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
        // $baseDomain = $storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB);

        // if (PHP_VERSION_ID >= 70300) {
        //     setcookie(
        //         '^(.*)',
        //         '',
        //         [
        //             'expires' => time() + 86400,
        //             'path'     => '/',
        //             'domain'   => $baseDomain,
        //             'secure' => true,
        //             'httponly' => true,
        //             'samesite' => 'None',
        //         ]
        //     );
        // }
        
        $this->_pageFactory = $pageFactory;
        $this->checkoutSession = $checkoutSession;
        $this->cart = $cart;
        parent::__construct($context);
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

    public function execute() {
        return $this->_pageFactory->create();
        // $resultRedirect = $this->resultRedirectFactory->create();
        // $resultRedirect->setPath('checkout/onepage/success');
        // return $resultRedirect;
    }

}
