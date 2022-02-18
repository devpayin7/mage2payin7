/*browser:true*/
/*global define*/
define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list',
        'jquery',
        'mage/cookies'
    ],
    function (
        Component,
        rendererList,
        $
    ) {
        'use strict';
        if($.cookie('payin7') !== ':(') {
            rendererList.push(
                {
                    type: 'mage2payin7',
                    component: 'Payin7_Mage2Payin7/js/view/payment/method-renderer/mage2payin7'
                }
            );
        }
        /** Add view logic here if needed */
        return Component.extend({});
    }
);
