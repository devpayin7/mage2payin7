<?php

namespace Payin7\Mage2Payin7\Controller\Ajax;

class Installments extends \Magento\Framework\App\Action\Action {

    protected $payin7, $checkoutSession, $savedQuoteFactory;

    public function __construct(
            \Magento\Framework\App\Action\Context $context,
            \Payin7\Mage2Payin7\Helper\Payin7 $payin7,
            \Magento\Checkout\Model\Session $checkoutSession,
            \Payin7\Mage2Payin7\Model\SavedQuoteFactory $savedQuoteFactory) {
        parent::__construct($context);    

        $this->payin7 = $payin7;
        $this->checkoutSession = $checkoutSession;
        $this->savedQuoteFactory = $savedQuoteFactory;
    }

    public function execute() {
        $savedQuote = $this->savedQuoteFactory->create();
        $savedQuote->load($this->checkoutSession->getQuote()->getId());
        
        $callback = $this->getRequest()->getParam('callback');
        $total = $this->getRequest()->getParam('grand_total');
        $payin7Data = array('attributeValues' => array(), 'price' => $total, 
            'commission' => $this->payin7->getCommissions(array($total)));
        $response = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_RAW);
        $response->setContents($callback.'('.json_encode(array('payin7Data' => $payin7Data, 
            'signature' => $this->payin7->createSignature($savedQuote->getTempId(), $total))).')');

        return $response;
    }

}
