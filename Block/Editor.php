<?php

namespace Payin7\Mage2Payin7\Block;

use Magento\Backend\Block\Template\Context;
use Magento\Cms\Model\Wysiwyg\Config as WysiwygConfig;

class Editor extends \Magento\Config\Block\System\Config\Form\Field {

    /**
     * @param Context       $context
     * @param WysiwygConfig $wysiwygConfig
     * @param array         $data
     */
    public function __construct(
            Context $context,
            WysiwygConfig $wysiwygConfig,
            array $data = []
    ) {
        $this->_wysiwygConfig = $wysiwygConfig;
        parent::__construct($context, $data);
    }

    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element) {
        // set wysiwyg for element
        $element->setWysiwyg(true);
        // set configuration values 
        $element->setConfig($this->_wysiwygConfig->getConfig($element));
        return parent::_getElementHtml($element);
    }

}
