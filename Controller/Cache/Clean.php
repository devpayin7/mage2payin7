<?php

namespace Payin7\Mage2Payin7\Controller\Cache;

use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Payin7\Mage2Payin7\Helper\CacheManager;

class Clean extends \Magento\Framework\App\Action\Action implements CsrfAwareActionInterface
{

    protected $cacheManager, $logger, $resultJsonFactory,$scopeConfig;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        CacheManager $cacheManager,
        JsonFactory $resultJsonFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Psr\Log\LoggerInterface $logger)
    {
        parent::__construct($context);

        $this->cacheManager = $cacheManager;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->logger = $logger;
        $this->scopeConfig=$scopeConfig;
    }

    /**
     * @inheritDoc
     */
    public function createCsrfValidationException(RequestInterface $request): ?InvalidRequestException
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function validateForCsrf(RequestInterface $request): ?bool
    {
        $signature = $request->getHeader('Signature');
        return $this->checkCallbackSignature($signature);
    }

    public function execute()
    {
        //Log
        $signature = $this->getRequest()->getHeader('Signature');
        $success = false;
        $this->logger->info('Cache callback', ['Signature' => $signature]);

        $affected_rows = 0;
        if ($this->checkCallbackSignature($signature)) {
            try {
                $affected_rows = $this->cacheManager->clean();
                $success = true;
            } catch (\Exception $e) {
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
            }
        }
        /** @var \Magento\Framework\Controller\Result\Json $result */
        $result = $this->resultJsonFactory->create();

        return $result->setData(['success' => $success, 'rows' => $affected_rows]);

    }

    /**
     * Comprueba la clave recibida de Payin7.
     * @return boolean
     */
    public function checkCallbackSignature($signature)
    {
        $calculateSignature = sha1(
            $this->scopeConfig->getValue('payment/mage2payin7/general/api_key') .
            $this->scopeConfig->getValue('payment/mage2payin7/general/integration_id'));
        return ($calculateSignature == $signature);
    }

}
