/* global showTinTae */

define([
    "jquery",
    'Magento_Catalog/js/price-utils',
    'mage/translate'
], function($, utils, __) {
    "use strict";

    return {
        /**
         * Indica si dos arrays contienen los mismos elementos.
         * @param {Array} a
         * @param {Array} b
         * @returns {Boolean}
         */
        isEqual: function(a, b) {
            // if length is not equal 
            if(a.length !== b.length) 
                return false; 
            else {
                // comparing each element of array 
                for(var i=0;i<a.length;i++) {
                    if(b.indexOf(a[i]) === -1) {
                        return false;
                    }
                }
                return true; 
            } 
        },

        /**
         * Devuelve los datos de comisiones.
         * @returns {Array}
         */
        getPayin7Data: function() {
            var found = false;
            var datos = $('.payin7_calculator').not('.productNo').data('payin7');

            if(datos) {
                var cantidadAtributos = $('.swatch-attribute').length;
                if(cantidadAtributos === 0) {
                    if(typeof datos.commission !== 'undefined') {
                        found = datos.commission[0];
                    }
                }
                else {
                    var valoresAtributo = [ ];
                    $('#product-options-wrapper div.selected').each(function() {
                        valoresAtributo.push($(this).attr('option-id'));
                    });

                    if(valoresAtributo.length === cantidadAtributos) {
                        for(var i in datos) {
                            if(this.isEqual(datos[i].attributeValues, valoresAtributo)) {
                                //Datos encontrados
                                found = datos[i].commission;
                            }
                        }
                    }
                }
            }

            return found;
        },

        /**
         * Actualiza los datos de la calculadora.
         * @param {String} price
         * @param {String} month
         * @param {String} tin
         * @param {String} tae
         */
        updateCalculator: function(price, month, tin, tae) {
            $('.payin7_calculator.product_calculator .calc-amount').html(utils.formatPrice(price));
            $('.payin7_calculator.product_calculator .installments-count').val(month);
            $('.payin7_calculator.product_calculator .calc-months').html(month);
            $('.payin7_calculator.product_calculator .calc-financial').html(utils.formatPrice(price * month));
            $('.payin7_calculator.product_calculator .calc-tin').html(tin);
            $('.payin7_calculator.product_calculator .calc-tae').html(tae);
        },

        /**
         * Actualiza la calculadora al cambiar la cantidad de cuotas.
         * @param {Object} payin7Data
         * @param {String} direction
         */
        updateCalculatorByInstallments: function(payin7Data, direction) {
            if(typeof payin7Data !== 'undefined') {
                var actualMonths = parseInt($('.installments-count').val()), newMonths;
                switch (direction) {
                    case 'down':
                        newMonths = actualMonths - 1;
                        break;
                    case 'up':
                        newMonths = actualMonths + 1;
                        break;
                    default:
                        newMonths = actualMonths;
                }

                if (newMonths >= payin7Data.minimum_installments && newMonths <= payin7Data.maximum_installments) {
                    var price = 0;
                    var tin = 0;
                    var tae = 0;
                    for(var i in payin7Data.installments) {
                        var iter = payin7Data.installments[i];
                        if (iter.months == newMonths) {
                             price = iter.amount;
                             tin = iter.tin;
                             tae = iter.tae;
                             break;
                        }
                    }

                    if(price) {
                        this.updateCalculator(price, newMonths, tin, tae);
                    }
                }
            }
        },

        /**
         * Actualiza la calcualdora al cambiar al combinacion.
         * @param {Object} payin7Data
         */
        updateCalculatorByCombination: function(payin7Data) {
            if(typeof payin7Data === 'undefined' || typeof payin7Data.installments === 'undefined') {
                $('.payin7_calculator').hide();
                $('.payin7_calculator.productNo').show();
            }
            else {
                for(var i in payin7Data.installments) {
                    var iter = payin7Data.installments[i];
                    if(typeof iter.default !== 'undefined' && iter.default) {
                        this.updateCalculator(iter.amount, iter.months, iter.tin, iter.tae);
                        break;
                    }
                }
                $('.payin7_calculator').show();
                $('.payin7_calculator.productNo').hide();
            }
        },

        showPopUp: function(html) {
            $(html).appendTo('body').modal({ autoOpen: true });
            $('.containerPayin7Finance').removeAttr('style');
        },

        createDataTable: function() {
            var data = this.getPayin7Data();
            var i = 1;
            var html = '<div class="containerPayin7Finance">';
            html += '<p class="financeTitle">' + __('Financia tu compra con Payin7 en el proceso de compra') + '</p>';
            html += '<p class="financeSubTitle">' + __('Puedes elegir cómo fraccionar tu compra en cómodos plazos:') + '</p>';
            html += '<ul>';
            $.each(data.installments, function (index, elemento) {
                html += '<li class="col-md-4 col-sm-4 col-xs-4">';
                html += '<div>';
                html += '<p><span class="calc-months">' + elemento.months + ' ' + __('cuotas') + '</span></p>';
                if (elemento.first_payment_amount) {
                    html += '<p><span class="calc-first_payment">' + utils.formatPrice(elemento.first_payment_amount) + ' ' + __('entrada') + '</span></p>';
                }
                html += '<p><span class="calc-amount">' + utils.formatPrice(elemento.amount) + ' / ' + __('mes') + '</span></p>';
                if(showTinTae) {
                    html += '<p>(' + elemento.tin + '% ' + __('TIN') + ', ' + elemento.tae + '% ' + __('TAE') + ')</p>';
                    html += '<p>' + __("Total financiado:") + ' ' + utils.formatPrice(elemento.months * elemento.amount) + '</p>';
                }
                html += '</div>';
                html += '</li>';

                i++;
            });
            html += '</ul>';
            html += '</div>';


            return html;
        }
    };
});