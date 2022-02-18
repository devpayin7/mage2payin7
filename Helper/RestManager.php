<?php


namespace Payin7\Mage2Payin7\Helper;


use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\ResourceConnection;

class RestManager extends \Magento\Framework\App\Helper\AbstractHelper
{
    const PAYMENT_MAGE_2_PAYIN_7_GENERAL_CALCULATOR_KEY = 'payment/mage2payin7/general/calculator_key';
    const PAYMENT_MAGE_2_PAYIN_7_GENERAL_PAYIN_7_API = 'payment/mage2payin7/general/payin7_api';
    const STORE_CACHE = '/store/cache';
    private $curl;

    public function __construct(
        Context $context,
        \Magento\Framework\HTTP\Client\Curl $curl
    )
    {
        parent::__construct($context);
        $this->curl = $curl;
        $this->curl->addHeader('Authorization', 'Bearer ' . $this->scopeConfig->getValue('' . self::PAYMENT_MAGE_2_PAYIN_7_GENERAL_CALCULATOR_KEY . ''));
        $this->curl->addHeader('Accept', 'application/json');
        $this->curl->addHeader('User-Agent', 'Informax');
    }

    public function checkCacheClean($timestamp)
    {
        $response = array('success' => false);
            //Solicitamos el cache
            try {
                $postData = array('timestamp' => $timestamp);
                $this->curl->post($this->scopeConfig->getValue('' . self::PAYMENT_MAGE_2_PAYIN_7_GENERAL_PAYIN_7_API . '') . '' . self::STORE_CACHE . '', http_build_query($postData));
                $response = array_merge($response, json_decode($this->curl->getBody(), true)) ;
                $response['success']=true;
            } catch (\Exception $e) {
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
            }
            return $response;
        }
}