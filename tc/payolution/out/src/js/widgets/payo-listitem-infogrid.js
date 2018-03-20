payolution_jQuery(function ($) {
    $('.payoOldPrice').each(function () {
        var container = $(this);
        var durations = Payolution.getAvailableDurations();

        var price = container.data('price');

        if (price >= Payolution.getMinimumAmount() && price <= Payolution.getMaximumAmount()) {
            container.find('.payoPrice')
                .html(installmentSlider.formatCurrency(Payolution.calculateInstallment(price, durations[durations.length - 1]).installmentAmount)
            );
            container.show();
        } else {
            container.hide();
        }
    });
});
