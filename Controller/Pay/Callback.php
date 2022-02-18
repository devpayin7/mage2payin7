<?php

namespace Payin7\Mage2Payin7\Controller\Pay;

use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Request\InvalidRequestException;

class Callback extends \Magento\Framework\App\Action\Action implements CsrfAwareActionInterface {

    protected $_quoteFactory, $quoteManagement, $orderSender, $payin7, $_invoiceService, $_transaction, $scopeConfig, $_transportBuilder, 
            $savedQuoteFactory, $logger, $invoiceSender;

    public function __construct(
            \Magento\Framework\App\Action\Context $context,
            \Magento\Quote\Model\QuoteFactory $quoteFactory,
            \Magento\Quote\Model\QuoteManagement $quoteManagement,
            \Magento\Sales\Model\Order\Email\Sender\OrderSender $orderSender,
            \Payin7\Mage2Payin7\Helper\Payin7 $payin7,
            \Magento\Sales\Model\Service\InvoiceService $invoiceService,
            \Magento\Framework\DB\Transaction $transaction,
            \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
            \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
            \Payin7\Mage2Payin7\Model\SavedQuoteFactory $savedQuoteFactory,
            \Psr\Log\LoggerInterface $logger,
            \Magento\Sales\Model\Order\Email\Sender\InvoiceSender $invoiceSender) {
        parent::__construct($context);    
        
        $this->_quoteFactory = $quoteFactory;
        $this->quoteManagement = $quoteManagement;
        $this->orderSender = $orderSender;
        $this->payin7 = $payin7;
        $this->_invoiceService = $invoiceService;
        $this->_transaction = $transaction;
        $this->scopeConfig = $scopeConfig;
        $this->_transportBuilder = $transportBuilder;
        $this->savedQuoteFactory = $savedQuoteFactory;
        $this->logger = $logger;
        $this->invoiceSender = $invoiceSender;
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
        //Log
        $this->logger->info('Payin7 callback', $this->getRequest()->getParams());

        
        $idQuote = $this->getRequest()->getParam('store_data');
        $order_state = $this->getRequest()->getParam('order_state');
        $signature = $this->getRequest()->getParam('signature');
        $order_id = $this->getRequest()->getParam('order_id');
        $quote = $this->_quoteFactory->create()->load($idQuote);

        $getSignature = $this->payin7->getCallbackSignature($order_id, $order_state, $quote);
        $this->logger->info('Payin7 signature get:',['signature'=>$getSignature]);

        if($idQuote && $this->payin7->checkCallbackSignature($order_id, $order_state, $quote, $signature)) {
            $savedQuote = $this->savedQuoteFactory->create();
            $savedQuote->load($quote->getId());
            
            if($order_state == 'active') {
                if($quote->getIsActive() && !$savedQuote->getCreatingOrder()) {
                    $savedQuote->setCreatingOrder(1);
                    $savedQuote->save();
                    
                    //Creamos el pedido
                    $quote->getPayment()->importData(['method' => 'mage2payin7']);
                    $quote->collectTotals()->save();
                    $order = $this->quoteManagement->submit($quote);
                    if($order->getIncrementId()) {
                        $order->setState('processing')->setStatus('processing');
                        //Creamos la factura
                        if($order->canInvoice()) {
                            $invoice = $this->_invoiceService->prepareInvoice($order);
                            $invoice->register();
                            $invoice->save();
                            $transactionSave = $this->_transaction->addObject(
                                $invoice
                            )->addObject(
                                $invoice->getOrder()
                            );
                            $transactionSave->save();
                            $this->invoiceSender->send($invoice);
                            //send notification code
                            $order->addStatusHistoryComment(
                                __('Notified customer about invoice #%1.', $invoice->getId())
                            )
                            ->setIsCustomerNotified(true)
                            ->save();
                        }
                        else {
                            $this->logger->warning('Payin7 callback - Imposible facturar el pedido.');
                        }
                        
                        //Actualizamos el id de pedido en Payin7
                        $this->payin7->updateStoreOrderId($savedQuote->getTempId(), $quote->getId());

                        //Enviamos el email
                        $this->orderSender->send($order);
                    }
                    else {
                        $this->logger->warning('Payin7 callback - Error al crear el pedido.');
                    }
                }
                else {
                    $this->logger->warning('Payin7 callback - Ya existe el pedido o el carrito no está activo.');
                }
            }
            elseif($order_state == 'paid') {
                if(!$savedQuote->getPaidEmail()) {
                    $savedQuote->setPaidEmail(1);
                    $savedQuote->save();
                    
                    //Enviamos email de aviso
                    try {
                        $fullName = $quote->getBillingAddress()->getFirstname().' '.$quote->getBillingAddress()->getMiddlename().' '.$quote->getBillingAddress()->getLastname();

                        $transport = $this->_transportBuilder
                            ->setTemplateIdentifier('payin7paid_email_template')
                            ->setTemplateOptions([
                                'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                                'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                            ])
                            ->setTemplateVars([
                                'name' => $fullName
                            ])
                            ->setFrom([
                                'name' => $this->scopeConfig->getValue('trans_email/ident_general/name', \Magento\Store\Model\ScopeInterface::SCOPE_STORE),
                                'email' => $this->scopeConfig->getValue('trans_email/ident_general/email', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)
                            ])
                            ->addTo(
                                    $quote->getBillingAddress()->getEmail(),
                                    $fullName
                            )
                            ->getTransport();

                            $transport->sendMessage();
                    } 
                    catch(\Exception $e) { }
                }
            }
        }
        else {
            $this->logger->warning('Payin7 callback - Error en signature.');
        }

        return '';
    }

}
