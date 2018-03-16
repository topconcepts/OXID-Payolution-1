payolution_jQuery(function($){
    var modal = $('#payoInstallmentModal');
    var sumVal = modal.find('#payo-order-sum');
    var sum = modal.find('#payo-installment-sum');

    $('.payoReadMore').oxModalPopup({
        target: '#payoInstallmentModal',
        width: 500
    }).bind('click', function(){
        var $this = $(this);

        sumVal.val($this.parent().data('price'));
        installmentSlider.setPrice(parseFloat($('#payo-order-sum').val()));
        sum.html(installmentSlider.formatCurrency($this.parent().data('price')));
    });
});
