<?php

namespace Payin7\Mage2Payin7\Block;

class ColorPicker extends \Magento\Config\Block\System\Config\Form\Field {

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
            \Magento\Backend\Block\Template\Context $context,
            array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * add color picker in admin configuration fields
     * @param  \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string script
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element) {
        $html = $element->getElementHtml();
        $value = $element->getData('value');

        $html .= '<script type="text/javascript">
            require(["jquery"], function ($) {
                $(document).ready(function (e) {
                    $("#'.$element->getHtmlId().'").css("background-color","#'.$value.'");
                    $("#'.$element->getHtmlId().'").colpick({
                        layout:"hex",
                        submit:0,
                        colorScheme:"dark",
                        color: "#'.$value.'",
                        onChange:function(hsb,hex,rgb,el,bySetColor) {
                        $(el).css("background-color","#"+hex);
                        if(!bySetColor) $(el).val(hex);
                    }
                    }).keyup(function(){
                        $(this).colpickSetColor(this.value);
                    });
                });
            });
            </script>';

        return $html;
    }

}
