payolution_jQuery(function($){
    var installmentContainer = $('#payo-order-installment-details');
    var togglerShow = $('#payo-toggler-show');
    var togglerHide = $('#payo-toggler-hide');


    installmentContainer.hide();
    togglerHide.hide();
    togglerShow.show();
    $('#payo-installment-sum').html(installmentSlider.formatCurrency($('#payo-order-sum').val()));
    installmentSlider.useServerSide = true;
    installmentSlider.init();
    installmentSlider.toggleDetails();

    $('.payo-order-installment-details-toggler').bind('click', function(){
        installmentContainer.toggle();
        togglerShow.toggle();
        togglerHide.toggle();
    });
});
