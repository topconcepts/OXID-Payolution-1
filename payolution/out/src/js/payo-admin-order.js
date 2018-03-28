jQuery(function($){
    var delivery            = $('#fullDelivery');
    var partialDelivery     = $('#partialDelivery');
    var refund              = $('#fullRefund');
    var refundAmount        = $('#refundAmount');
    var refundPercentage    = $('#refundPercentage');
    var fullCancel          = $('#fullCancel');
    var pdf                 = $('#invoicePdf');

    /**
     * Switch between full/partial delivery buttons
     */
    function setDeliveryButton() {
        if ($('.payo-order-article-check.delivery:checked').size() > 0) {
            partialDelivery.show();
        } else {
            partialDelivery.hide();
        }
    }

    function updateRefundButtonAvailability(value) {
        if (!value) {
            refund.attr("disabled", "disabled");
        } else {
            refund.removeAttr("disabled");
        }
    }

    /**
     * @returns float
     */
    function getOriginalRefundAmount()
    {
        return extractPrice(refundAmount.data('original-amount'));
    }

    /**
     * @returns float
     */
    function extractPrice(price) {
        if (sessionCurrency.thousand && price.indexOf(sessionCurrency.thousand) !== -1
            && price.indexOf(sessionCurrency.dec) === -1) {
            price = price.replace(sessionCurrency.thousand, sessionCurrency.dec);
        } else if (!sessionCurrency.thousand && price.indexOf(sessionCurrency.dec) === -1) {
            var symbol = ',';
            if (sessionCurrency.dec == symbol) {
                symbol = '.';
            }
            price = price.replace(symbol, sessionCurrency.dec);
        }

        return accounting.unformat(price, sessionCurrency.dec);
    }

    /**
     * Whether or not given key is a decimal part separator (, or .)
     *
     * @param {number} key
     * @returns {boolean}
     */
    function isDecimalChar(key) {
        return key == 110
        || key == 190
        || key == 188
    }

    /**
     * @param price
     * @returns {string}
     */
    function formatPrice(amount)
    {
        return accounting.formatMoney(amount, "", sessionCurrency.decimal, sessionCurrency.thousand, sessionCurrency.dec, sessionCurrency.side != undefined ? '%s %v' : '%v %s').trim();
    }

    function getPriceEntered()
    {
        var entered = refundAmount.val();
        return extractPrice(entered);
    }

    /**
     * Update refund amount field with data collected from shipped items table and amounts entered
     */
    function updateRefundAmountFromTable() {
        var price = 0,
            amount = 0,
            allowedAmount =  extractPrice(refundAmount.data('original-amount'));
        if($('.payo-order-article-check:not(.delivery):checked').size() == 0) {
            refundPercentage.val(100).trigger('keyup');
        } else {
            $('.payo-order-article-check:not(.delivery)').each(function(){
                var $this = $(this);
                var amountField = $this.parent().next('td').find('.amount');
                var amount = parseFloat($this.prop('checked') ? amountField.val() : 0);

                price += amount * parseFloat(amountField.data('price'));
            });
            amount = Math.round(price * 100) / 100;

            if (amount > allowedAmount ) {
                amount = allowedAmount;
                refundAmount.val(formatPrice(allowedAmount));
            } else if (amount < 0) {
                amount = 0;
                refundAmount.val(0);
            }

            refundAmount.data("current-amount", formatPrice(amount));
            refundAmount.val(formatPrice(amount)).trigger('keyup');
        }
    }

    /**
     * Show/hide quantity input fields on checkbox state change
     */
    $('.payo-order-article-check').bind('change', function () {
        var checkbox = $(this);
        var amount = checkbox.parent().next('td').find('input.amount');
        var text = checkbox.parent().next('td').find('span');
        setDeliveryButton();

        if (checkbox.prop('checked')) { // Show amount fields
            amount.show();
            text.hide();
        } else if (amount.val() == amount.data("original-amount")) { // Allow disable only when amount entered is the same as original amount
            amount.hide();
            text.show();
        } else { // Prevent from disabling checkbox if amount entered is not the same as original amount
            checkbox.prop('checked', true).attr('checked', 'checked');
            return false;
        }
    });

    // Prevent from entering higher than original amount and update delivery button state
    $('.amount').bind('keyup', function (e) {
        var $this = $(this);
        if($this.val().length && !isDecimalChar(e.keyCode)) {
            var min = Math.min($this.val(), $this.data('original-amount'));
            $this.val(isNaN(min) ? $this.data('original-amount') : min);
        }

        if($this.hasClass('amountRef')) {
            updateRefundAmountFromTable();
        }
    });

    /**
     * format amount
     */
    refundAmount.bind("focusout", function(e) {
        $(this).val(formatPrice(getPriceEntered()));
    });

    /**
     * Refund amount <-> Refund percentage
     */
    refundAmount.bind('blur', function (e) {
        var $this = $(this),
            allowedAmount = extractPrice(refundAmount.data('original-amount')),
            amountEntered = getPriceEntered();

        if (amountEntered == extractPrice($this.data("current-amount"))) {
            return;
        }

        if (allowedAmount < amountEntered) {
            amountEntered = allowedAmount;
            refundAmount.val(allowedAmount);
        } else if (amountEntered < 0) {
            amountEntered = 0;
            refundAmount.val(0);
        }

        if (e.keyCode != undefined && amountEntered) {
            $this.data("current-amount", formatPrice(amountEntered));
        }

        var value = Math.ceil(amountEntered / allowedAmount * 100);
        refundPercentage.val(isNaN(value) ? 100 : value); 
        $this.val(formatPrice(amountEntered));
        setDeliveryButton();
        updateRefundButtonAvailability(value);
    });
    
    refundPercentage.bind('keyup', function (e) {
        var $this = $(this);
        if($this.val().length && !isDecimalChar(e.keyCode)) {
            $this.val(Math.min(100, $this.val()));
        }
        var value = Math.round($this.val() * getOriginalRefundAmount()) / 100;
        var amount = isNaN(value) ? getOriginalRefundAmount() : value;
        refundAmount.data("current-amount", amount);
        refundAmount.val(formatPrice(amount));
        setDeliveryButton();
    });

    /**
     * Order article refund amount -> Refund amount
     */
    $('.payo-order-article-check:not(.delivery)').bind('change', function(){
        updateRefundAmountFromTable();
    });

    /**
     * Partial delivery
     */
    partialDelivery.bind('click', function(){
        $('input[name="fnc"]').val('shipOrderPartially');
        $(this).closest('form').submit();
    });

    /**
     * Full delivery
     */
    delivery.bind('click', function(){
        $('input[name="fnc"]').val('shipOrder');
        $(this).closest('form').submit();
    });

    /**
     * Refund
     */
    refund.bind('click', function(){
        refundAmount.val(getPriceEntered());
        $('input[name="fnc"]').val('refundOrder');
        $(this).closest('form').submit();
    });

    /**
     * Full deliver
     */
    fullCancel.bind('click', function(){
        $('input[name="fnc"]').val('cancelOrder');
        $(this).closest('form').submit();
    });


    pdf.bind('click', function () {
        $('input[name="fnc"]').val('pdf');
        $(this).closest('form').submit();
    });


    // Init on load
    setDeliveryButton();
});
