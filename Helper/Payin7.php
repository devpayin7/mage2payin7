<?php

namespace Payin7\Mage2Payin7\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class Payin7 extends AbstractHelper
{
    private $curl, $commissionFactory,$cacheManager;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\HTTP\Client\Curl $curl,
        \Payin7\Mage2Payin7\Model\CommissionFactory $commissionFactory,
        CacheManager $cacheManager)
    {
        parent::__construct($context);

        $this->cacheManager=$cacheManager;

        $this->curl = $curl;
        $this->curl->addHeader('Authorization', 'Bearer ' . $this->scopeConfig->getValue('payment/mage2payin7/general/calculator_key'));
        $this->curl->addHeader('Accept', 'application/json');
        $this->curl->addHeader('User-Agent', 'Informax');
        $this->commissionFactory = $commissionFactory;
    }

    /**
     * Genera la clave para enviar con el formulario de Payin7.
     * @param string $payin7_order_id
     * @param float $order_total
     * @return string
     */
    public function createSignature($payin7_order_id, $order_total)
    {
        return sha1($this->scopeConfig->getValue('payment/mage2payin7/general/api_key') . $this->scopeConfig->getValue('payment/mage2payin7/general/integration_id') . $payin7_order_id . number_format($order_total, 4, '.', ''));
    }

    /**
     * Comprueba la clave recibida de Payin7.
     * @param string $order_id
     * @param string $order_state
     * @param \Magento\Quote\Model\Quote $quote
     * @return boolean
     */
    public function checkCallbackSignature($order_id, $order_state, $quote, $signature)
    {
        $calculateSignature = sha1(
            $this->scopeConfig->getValue('payment/mage2payin7/general/api_key') .
            $this->scopeConfig->getValue('payment/mage2payin7/general/integration_id') .
            $order_id .
            $order_state .
            number_format($quote->getGrandTotal(), 4, '.', '') .
            $quote->getItemsCount());
        return ($calculateSignature == $signature);
    }

    /**
     * Obtiene la clave Payin7.
     * @param string $order_id
     * @param string $order_state
     * @param \Magento\Quote\Model\Quote $quote
     * @return string
     */
    public function getCallbackSignature($order_id, $order_state, $quote)
    {
        $calculateSignature = sha1(
            $this->scopeConfig->getValue('payment/mage2payin7/general/api_key') .
            $this->scopeConfig->getValue('payment/mage2payin7/general/integration_id') .
            $order_id .
            $order_state .
            number_format($quote->getGrandTotal(), 4, '.', '') .
            $quote->getItemsCount());
        return ($calculateSignature);
    }

    /**
     * Devuelve las comisiones para unos precios.
     * @param float[] $amounts
     * @return array
     */
    public function getCommissions($amounts)
    {
        $finalAmounts = array();
        $emptyAmounts = array();
        $newTimestamp = time();
        $maxTimestamp = time() + $this->getEvictCacheTime();
        //TODO: Check cache clean, perhaps better in Cron
        $this->cacheManager->checkCacheClean();
        //Cargamos lo disponible en base de datos
        foreach (array_unique($amounts) as $amount) {
            $commission = $this->commissionFactory->create();
            $commission->load($amount);
            $timestamp = $commission->getData('timestamp');
            if ($commission->isObjectNew() || empty($timestamp) || $timestamp < $newTimestamp) {
                $emptyAmounts[] = $amount;
            } else {
                $rawData = $commission->getData();
                $finalAmounts[] = unserialize($rawData['commission']);
            }
        }

        if ($emptyAmounts) {
            //Solicitamos las comisiones restantes
            $postData = array('amount' => $emptyAmounts);
            $this->curl->post($this->scopeConfig->getValue('payment/mage2payin7/general/payin7_api') . '/store/calculate_commissions_multi', http_build_query($postData));
            $response = json_decode($this->curl->getBody(), true);
            if (!empty($response['data'])) {
                //Nuevas a base de datos
                foreach ($response['data'] as $data) {
                    $commission = $this->commissionFactory->create();
                    $commission->setData(array('amount' => $data['amount'], 'commission' => serialize($data), 'timestamp' => $maxTimestamp));
                    $commission->save();
                }

                $finalAmounts = array_merge($finalAmounts, $response['data']);
            }
        }

        return $finalAmounts;
    }

    /**
     * Modifica el id de un pedido.
     * @param int $oldId
     * @param int $newId
     * @return string
     */
    public function updateStoreOrderId($oldId, $newId)
    {
        $this->curl->post($this->scopeConfig->getValue('payment/mage2payin7/general/payin7_api') . "/store/update_store_order_id/$oldId/$newId", '');

        return json_decode($this->curl->getBody());
    }

    /**
     * @return evict cache time in seconds
     */
    public function getEvictCacheTime()
    {
        return ((integer)$this->scopeConfig->getValue('payment/mage2payin7/calculadora/evictCache'));
    }
}
