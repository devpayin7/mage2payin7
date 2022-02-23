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

        $html .= '<script>
            require(["jquery", "jquery/colorpicker/js/colorpicker", "domReady!"], function ($) {
                var el = $("#'.$element->getHtmlId().'");
                
                el.css("background-color", "#'.$value.'");
                el.ColorPicker({
                    layout: "hex",
                    submit: 0,
                    colorScheme: "dark",
                    color: "#'.$value.'",
                    onChange: function (hsb, hex, rgb) {
                        el.css("background-color", "#"+hex);
                    }
                }).keyup(function() {
                    $(this).ColorPickerSetColor(this.value);
                });
            });
            </script>';

        return $html;
    }

}
