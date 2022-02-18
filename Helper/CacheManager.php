<?php


namespace Payin7\Mage2Payin7\Helper;


use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\ResourceConnection;

class CacheManager extends \Magento\Framework\App\Helper\AbstractHelper
{
    private $restManager, $cacheFactory;

    public function __construct(
        Context $context,
        ResourceConnection $resourceConnection,
        RestManager $restManager,
        \Payin7\Mage2Payin7\Model\CacheFactory $cacheFactory
    )
    {
        parent::__construct($context);
        $this->resourceConnection = $resourceConnection;
        $this->cacheFactory = $cacheFactory;
        $this->restManager=$restManager;
    }

    /**
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function clean()
    {
        $affected_rows = 0;
        try {
            $connection = $this->resourceConnection->getConnection();
            $cacheTable = $this->resourceConnection->getTableName('mage2payin7_commission');
            $sql = "DELETE FROM $cacheTable WHERE amount>0";
            $result = $connection->query($sql);
            $affected_rows = $result->rowCount();
        } catch (\Exception $e) {
            $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
        }

        return $affected_rows;
    }

    public function checkCacheClean()
    {
        $newTimestamp = time();
        $maxTimestamp = time() + $this->getEvictCacheTime();
        $cache = $this->cacheFactory->create();
        $cache->load(0); // always 0
        $timestamp = $cache->getData('timestamp');
        if ($cache->isObjectNew() || $timestamp < $newTimestamp) {
            //Solicitamos el cache
            try {
                $response=$this->restManager->checkCacheClean($timestamp);
                if (!empty($response) && $response['success'] && array_key_exists('clean',$response) &&$response['clean']) {
                    $this->clean();
                }
            } catch (\Exception $e) {
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
            }
        }
        $cache->setData('timestamp',$maxTimestamp);
        $cache->save();
    }

    /**
     * @return evict cache time in seconds, ten times configuration value
     */
    public function getEvictCacheTime()
    {
        return ((integer)$this->scopeConfig->getValue('payment/mage2payin7/calculadora/evictCache') * 10);
    }

}