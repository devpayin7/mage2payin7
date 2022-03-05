/* global positionParent, position */

require([
    'jquery',
    'payin7js_utils'
], function ($, payin7Utils) {
    //Posicionamos la calculadora
    if (positionParent) {
        switch (position) {
            case '1':
                $(positionParent).prepend($('.payin7_calculator'));
                break;
            case '2':
                $('.payin7_calculator').appendTo($(positionParent));
                break;
        }
    }
    
    //Cambio de combinacion
    $('.swatch-opt').on('change', function() {
        var datos = payin7Utils.getPayin7Data();
        payin7Utils.updateCalculatorByCombination(datos);
    });
    
    $(document).on('swatch.initialized', function() {
        var datos = payin7Utils.getPayin7Data();
        payin7Utils.updateCalculatorByCombination(datos);
    });
    if(!$('.payin7_calculator').data('is_cart')) {
        var datos = payin7Utils.getPayin7Data();
        payin7Utils.updateCalculatorByCombination(datos);
    }

     //Pulsar en menos
     $(document).on('click', '.payin7_calculator .btn.btn-minus', function (e) {
         var datos = payin7Utils.getPayin7Data();
         payin7Utils.updateCalculatorByInstallments(datos, 'down');
     });

     //Pulsar en mas
     $(document).on('click', '.payin7_calculator .btn.btn-plus', function (e) {
         var datos = payin7Utils.getPayin7Data();
         payin7Utils.updateCalculatorByInstallments(datos, 'up');
     });

     //Efectos de boton
     $(document).on('mousedown', '.payin7_calculator .calc-body .btn.btn-minus', function (e) {
         $('.payin7_calculator .calc-body .btn.btn-minus').addClass('mousedown');
     });

     $(document).on('mousedown', '.payin7_calculator .calc-body .btn.btn-plus', function (e) {
         $('.payin7_calculator .calc-body .btn.btn-plus').addClass('mousedown');
     });

     $(document).on('mouseup', '.payin7_calculator .calc-body .btn.btn-minus', function (e) {
         $('.payin7_calculator .calc-body .btn.btn-minus').removeClass('mousedown');
     });

     $(document).on('mouseup', '.payin7_calculator .calc-body .btn.btn-plus', function (e) {
         $('.payin7_calculator .calc-body .btn.btn-plus').removeClass('mousedown');
     });

     //Boton de info
     $(document).on('click', '.payin7_calculator .icon-info, .payin7_calculator .info', function (e) {
         payin7Utils.showPopUp(payin7Utils.createDataTable());
     });

     $(document).on('click', '.modal-backdrop, .containerPayin7Finance.in', function (e) {
         $('.modal-backdrop').modal('hide');
         $('.containerPayin7Finance').modal('hide');
         $('.containerPayin7Finance').remove();
     });
});