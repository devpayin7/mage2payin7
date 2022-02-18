<?php

namespace Payin7\Mage2Payin7\Controller\Adminhtml\Clean;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Payin7\Mage2Payin7\Helper\CacheManager;

class Cache extends Action
{

    protected $resultJsonFactory;
    protected $cacheManager;


    /**
     * Cache constructor.
     * @param Context $context
     * @param ResourceConnection $resourceConnection
     * @param JsonFactory $resultJsonFactory
     */
    public function __construct(
        Context $context,
        CacheManager $cacheManager,
        JsonFactory $resultJsonFactory
    )
    {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->cacheManager=$cacheManager;
        parent::__construct($context);
    }

    /**
     * Cache relations data
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $affected_rows=0;
        $success = false;
        try {
            $affected_rows = $this->cacheManager->clean();
            $success = true;
        } catch (\Exception $e) {
            $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
        }
        /** @var \Magento\Framework\Controller\Result\Json $result */
        $result = $this->resultJsonFactory->create();

        return $result->setData(['success' => $success, 'rows' => $affected_rows]);
    }
}
?>