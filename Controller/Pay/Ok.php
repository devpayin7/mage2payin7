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
            CustomerCart $cart) {
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
        $this->_redirect('checkout/onepage/success');

        return $this->_pageFactory->create();
    }

}
