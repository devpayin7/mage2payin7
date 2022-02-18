/*browser:true*/
/*global define*/
define(
    [
        'Magento_Checkout/js/view/payment/default',
        'Magento_Checkout/js/model/quote',
        'mage/url',
        'jquery',
        'payin7js_utils'
    ],
    function (Component, quote, url, $, payin7Utils) {
        'use strict'; 

        var call;
        quote.totals.subscribe(function(totals){
            if(typeof call !== 'undefined') {
                call.abort();
            }
            call = $.getJSON(url.build('payin7/ajax/installments') + '?callback=?', {
                grand_total: totals.base_grand_total
            }, function(response) {
                if(response) {
                    //Actualizamos la calculadora
                    $('.payin7_calculator').data('payin7', response.payin7Data);
                    payin7Utils.updateCalculatorByInstallments(payin7Utils.getPayin7Data(), '');

                    //Actualizamos el formulario
                    if(quote.shippingMethod()) {
                        $('#imaxpayin7form input[name="order[shipping_method_code]"]').val(quote.shippingMethod().carrier_code);
                        $('#imaxpayin7form input[name="order[shipping_method_title]"]').val(quote.shippingMethod().carrier_title);
                    }
                    $('#imaxpayin7form input[name="signature"]').val(response.signature);
                    $('#imaxpayin7form input[name="order[tax]"]').val(parseFloat(totals.tax_amount).toFixed(4));
                    $('#imaxpayin7form input[name="order[shipping_with_tax]"]').val(parseFloat(totals.shipping_incl_tax).toFixed(4));
                    $('#imaxpayin7form input[name="order[shipping]"]').val(parseFloat(totals.shipping_amount).toFixed(4));
                    $('#imaxpayin7form input[name="order[shipping_discount]"]').val(parseFloat(totals.shipping_discount_amount).toFixed(4));
                    $('#imaxpayin7form input[name="order[total]"]').val(parseFloat(totals.base_grand_total).toFixed(4));
                }
            });
        }, null, 'change');
        quote.totals.valueHasMutated();

        return Component.extend({
            defaults: {
                template: 'Payin7_Mage2Payin7/payment/form'
            },

            getCode: function() {
                return 'mage2payin7';
            },

            getData: function() {
                return {
                    'method': this.item.method,
                    'additional_data': { }
                };
            },

            getConfig: function(key) {
                return (window.checkoutConfig.payment.mage2payin7[key] ? window.checkoutConfig.payment.mage2payin7[key] : 'XXXXX');
            },
            
            noEmpty: function(value) {
                return (value ? value : 'XXXXX');
            },
            
            getUrl: function(path) {
                return (path ? url.build(path) : 'XXXXX');
            },
            
            getGender: function() {
                var genero;
                switch(parseInt(window.checkoutConfig.customerData.gender)) {
                    case 1:
                        genero = 'male';
                        break;
                        
                    case 2:
                        genero = 'female';
                        break;
                        
                    default:
                        genero = 'male';
                        break;
                }
                
                return genero;
            },
            
            getShipping: function(key) {
                return (quote.shippingAddress() && quote.shippingAddress()[key] ? quote.shippingAddress()[key] : 'XXXXX');
            },
            
            getBilling: function(key) {
                return (quote.billingAddress() && quote.billingAddress()[key] ? quote.billingAddress()[key] : 'XXXXX');
            },
            
            getGuestEmail: function() {
                return quote.guestEmail;
            },
            
            getShippingMethod: function() {
                return quote.shippingMethod();
            },
            
            getQuoteTotals: function(key) {
                return (quote.totals() && quote.totals()[key] ? quote.totals()[key] : 0);
            },
            
            showByeMessage: function(data, ev) {
                if(window.checkoutConfig.payment.mage2payin7.despedida && !window.redireccionando) {
                    ev.preventDefault();

                    $('#payin7Overlay').show();
                    $('#payin7ByeMessage').show();
                    $('#payin7ByeMessage').css({ left: ($(window).width() / 2 - $('#payin7ByeMessage').outerWidth() / 2) + 'px' });

                    setTimeout(function() {
                        window.redireccionando = true;
                        $('#imaxpayin7form').submit();
                    }, window.checkoutConfig.payment.mage2payin7.tiempoDespedida * 1000);
                    
                    return false;
                }
                else {
                    return true;
                }
            }
        });
    }
);