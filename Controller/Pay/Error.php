<?php

namespace Payin7\Mage2Payin7\Controller\Pay;

use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\Session\SessionManagerInterface;

class Error extends \Magento\Framework\App\Action\Action implements CsrfAwareActionInterface
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    public function __construct(SessionManagerInterface $checkoutSession)
    {
        $this->checkoutSession = $checkoutSession;
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
        $this->checkoutSession->getQuote()->setIsActive(false);
        $this->checkoutSession->getQuote()->save();

        $this->_redirect('checkout/cart');
    }

}
