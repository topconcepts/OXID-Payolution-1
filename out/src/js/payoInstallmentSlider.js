var installmentSlider;
payolution_jQuery(function ($) {
    function cachePeriod(p) {
        $.cookie('cache-payolution-slider', p, {path: '/'});
    }


    function getCachedPeriod() {
        var value = $.cookie('cache-payolution-slider');

        return value ? value : null;
    }

    /**
     * Payolution installment payment slider controller
     *
     * @type {{initialized: boolean, url: {CLRequest: string}, init: Function}}
     */
    installmentSlider = {
        // Flags
        initialized  : false,

        // *) if set true we'll use server side for getting installment options.
        useServerSide: false,

        // Url`s, this url is used only if `useServerSide` property is set to true.
        url          : {
            CLRequest   : 'index.php?cl=payolution_order&fnc=cl',
            paramOxidCl : 'order',
            paramOxidFnc: 'cl'
        },

        slider            : null,

        // Slider params
        installmentPeriods: [],
        price             : 0,
        currencySymbol    : '€',
        thousandSeparator : '.',
        decimalSeparator  : ',',
        decimalPlaces     : 2,
        format            : '%v %s',
        percentageSymbol  : '%',

        selectedPeriod: null,
        valueField    : null,
        loader        : null,
        wrapper       : null,
        tabs          : null,

        dataTable: {
            sum              : null,
            period           : null,
            interestRate     : null,
            effectiveInterest: null,
            totalSum         : null
        },

        details: {
            container     : null,
            toggler       : null,
            visible       : false,
            dueTranslation: 'due',
            rateTranslation: (typeof installmentSliderRateTranslation != 'undefined') ? installmentSliderRateTranslation : ''
        },


        pdfLink          : null,
        privacyPolicyLink: null,
        termsLink        : null,

        durations                : [],
        serverSideInstallmentInfo: null, // <- here will be retrieved from serverside as assoc array {duration: info, duration: info}

        /**
         * Load installment periods
         *
         * @returns Promise<int[]>
         */
        getInstallments: function () {
            var promise = $.Deferred();

            if (this.useServerSide) {

                if (this.durations.length == 0) {
                    this.showLoader();
                    $('#payo-panel-installmentPeriod').css({'opacity' : 0});
                    $.post(
                        this.url.CLRequest,
                        {
                            price: this.price,
                            cl   : this.url.paramOxidCl,
                            fnc  : this.url.paramOxidFnc
                        },
                        $.proxy(function (response) {

                            if (response.status == 'ok') {
                                this.durations = response.durations;
                                this.serverSideInstallmentInfo = response.serverSideInstallmentInfo;
                                promise.resolve(response.durations); // <- resolve our future/promise with received duration list.
                                this.hideLoader();
                                $('#payo-panel-installmentPeriod').css({'opacity' : 1});
                            } else {
                                alert('Got error from server: ' + JSON.stringify(response));
                            }
                            
                            this.updatePdfLink();
                        }, this),
                        'json'
                    ).fail(function (response) {
                            // error
                        });
                }

            } else {

                this.durations = Payolution.getAvailableDurations();

                // <- resolve immediately, using client only calculation.
                promise.resolve(this.durations);
            }

            return promise.promise();
        },

        /**
         * Show loader background
         */
        showLoader: function () {
            this.tabs.hide();
            this.loader
                .width(this.wrapper.width() * 2)
                .height(this.wrapper.height() * 2)
                .show().addClass('payo-loader-flow');
            this.wrapper.addClass('payo-installment-wrapper-flow');
        },

        /**
         * Hide loader background
         */
        hideLoader: function () {
            this.tabs.show();
            this.loader.fadeTo('fast', 0, function () {
                $(this).hide().removeClass('payo-loader-flow');
            });
            this.wrapper.removeClass('payo-installment-wrapper-flow')
        },

        updatePdfLink : function () {
            var base64cmp = $('#base64cmp').val(),
                activeLanguageAbbreviationParameter = (typeof activeLanguageAbbreviation !== 'undefined') ? ('&lang='+activeLanguageAbbreviation) : '',
                activeBillingCountryParameter = (typeof activeBillingCountry !== 'undefined') ? ('territory='+activeBillingCountry) : '',
                infoPortLink = 'https://payment.payolution.com/payolution-payment/infoport/dataprivacydeclaration?' +
                    activeBillingCountryParameter + activeLanguageAbbreviationParameter + '&mId=' + base64cmp;

            $('#privacyPdf').attr('data-url', infoPortLink);
        },
        
        /**
         * Recalculate values
         *
         * @param {number} price
         * @param {number} duration
         * @returns {*}
         */
        calculateInstallment: function (price, duration) {
            if (this.useServerSide) {
                if (!this.serverSideInstallmentInfo)
                    throw new Error("cannot call calculateInstallment() function then useServerSide=true; and getInstallments() is not called. first call getInstallments() and wait while it completes.");

                return this.serverSideInstallmentInfo[duration];
            } else {
                return Payolution.calculateInstallment(price, duration);
            }
        },

        /**
         * Update data table
         *
         * @param {Object} calculated
         */
        updateData: function (calculated) {

            this.dataTable.monthlySum.html(this.formatCurrency(calculated.installmentAmount));
            this.dataTable.interestRate.html(this.formatCurrency(calculated.interestRate, this.percentageSymbol));
            this.dataTable.effectiveInterest.html(this.formatCurrency(calculated.effectiveInterest, this.percentageSymbol));
            this.dataTable.totalSum.html(this.formatCurrency(calculated.totalAmount));
            this.updateDetails(calculated.installments);

            if (this.pdfLink.size()) {
                var pdfBaseUrl = this.pdfLink.data('pdf-base-url');
                if (pdfBaseUrl.indexOf('?') === -1) {
                    pdfBaseUrl = pdfBaseUrl + '?';
                }
                this.pdfLink.attr('href', pdfBaseUrl + '&duration=' + calculated.installments.length);
            }

            if (this.privacyPolicyLink.size()) {
                this.privacyPolicyLink.attr('data-url', this.serverSideInstallmentInfo[calculated.installments.length].privacyUrl);
            }


            if (this.termsLink.size()) {
                this.termsLink.attr('data-url', this.serverSideInstallmentInfo[calculated.installments.length].termsUrl);
            }
        },

        /**
         * Update details container
         *
         * @param {Object} details
         */
        updateDetails: function (details) {
            this.details.container.html('');

            var $this = this;
            $.each(details, function (i, installment) {
                var date = new Date(installment.dueDate);
                var month = '' + (date.getMonth() + 1);
                var day = '' + date.getDate();
                var pad = '00';

                $this.details.container.append(
                    (i + 1) +
                    '. '+$this.details.rateTranslation+': (' + $this.details.dueTranslation + ' ' +
                    (pad.substring(0, pad.length - day.length) + day) + '.' +
                    (pad.substring(0, pad.length - month.length) + month) + '.' +
                    date.getFullYear() +
                    ') ' + $this.formatCurrency(installment.amount) +
                    '<br/>');
            });
        },

        /**
         * Toggle details view
         */
        toggleDetails: function () {
            if (this.details.visible) {
                this.details.container
                    .css('overflow-y', 'hidden')
                    .height(30);
                this.details.toggler.html(this.details.toggler.data('show'));
                this.details.visible = false;
            } else {
                this.details.container
                    .css('overflow-y', 'auto')
                    .css('height', 'auto');
                this.details.toggler.html(this.details.toggler.data('hide'));
                this.details.visible = true;
            }
        },

        pipValues: function () {
            var self = this;
            var values = [];

            $.each(this.installmentPeriods, function (i, period) {
                var precalculation = self.calculateInstallment(self.price, period);

                values.push(self.formatCurrency(precalculation.installmentAmount));
            });

            return values;
        },

        /**
         * Initialize jQuery UI slider (pips plugin)
         * @link http://simeydotme.github.io/jQuery-ui-Slider-Pips/
         */
        initSlider: function () {
            var $this = this;
            var self = this;


            var selectedPeriodIndex = this.installmentPeriods.indexOf(String(this.selectedPeriod));

            if (selectedPeriodIndex < 0)
                selectedPeriodIndex = this.installmentPeriods.indexOf(Number(this.selectedPeriod));

            if (selectedPeriodIndex < 0)
                selectedPeriodIndex = this.installmentPeriods.length - 1;

            this.slider = $("#installment-slider").slider({
                min  : 0,
                max  : $this.installmentPeriods.length - 1,
                range: 'min',
                value: selectedPeriodIndex,
                rest : "label"
            })
                .slider('pips', {rest: 'label', labels: $this.installmentPeriods})
                .slider('float', {labels: $this.pipValues()})
                .bind('slide', function (e, ui) {
                    var period = $this.installmentPeriods[ui.value];
                    $this.valueField.val(period);
                    $this.dataTable.period.html(period);

                    $this.selectedPeriod = period;

                    cachePeriod(period);

                    try {
                        $this.updateData(self.calculateInstallment($this.price, period), period);
                    } catch (e) {
                        //console.error(e); // Todo display error message
                    }
                });
            $('a.ui-slider-handle.ui-state-default.ui-corner-all').append(
                $('<div/>')
                    .addClass('buttonDot')
            );
            $('.ui-slider-line').each(function () {
                var $this = $(this);
                var $new = $this.clone();
                $new.addClass('top');
                $this.before($new);
            });

            this.valueField.val(this.installmentPeriods[this.slider.slider('option', 'value')]);
        },

        /**
         * Format currency
         *
         * @param {number} amount
         * @param [symbol]
         * @returns {*}
         */
        formatCurrency: function (amount, symbol) {
            return accounting.formatMoney(amount, symbol !== undefined ? symbol : this.currencySymbol, this.decimalPlaces, this.thousandSeparator, this.decimalSeparator, this.format);
        },

        /**
         * Initialize slider
         */
        init: function () {
            if (!this.initialized) {
                this.dataTable.sum = $('#payo-installment-sum');
                this.dataTable.period = $('#payo-installment-period');
                this.dataTable.monthlySum = $('#payo-installment-monthly-sum');
                this.dataTable.interestRate = $('#payo-installment-interest-rate');
                this.dataTable.effectiveInterest = $('#payo-installment-effective-interest');
                this.dataTable.totalSum = $('#payo-installment-total-sum');
                this.details.container = $('#payo-installment-details');
                this.details.toggler = $('#payo-installment-details-toggler').bind('click', function () {
                    installmentSlider.toggleDetails();
                });
                this.price = parseFloat($('#payo-order-sum').val());
                this.valueField = $('input[name="dynvalue[payolution_installment_period]"]');
                this.pdfLink = $('#payo-pdf');
                this.loader = $('#payo-loader');
                this.wrapper = $('#payo-installment-wrapper');
                this.privacyPolicyLink = $('#privacyPdf');
                this.termsLink = $('#termsPdf');
                this.details.dueTranslation = $('#payo-translation-due').val();
                this.tabs = $('#payo-installment-tabs');

                var defaultPeriodField = $('#payo-order-payolution_installment_period');

                this.getInstallments().done($.proxy(function (periods) {
                    this.installmentPeriods = periods;


                    var defaultPeriod;

                    if (defaultPeriodField.size() > 0) {
                        defaultPeriod = defaultPeriodField.val();
                    } else {
                        var maxAllowedPeriod = this.installmentPeriods[this.installmentPeriods.length - 1];

                        defaultPeriod = maxAllowedPeriod;

                        var cachedPeriod = getCachedPeriod();

                        var maxAllowedPeriodInt = Number(maxAllowedPeriod);
                        var cachedPeriodInt = Number(cachedPeriod);

                        if (cachedPeriodInt && !isNaN(cachedPeriodInt)) {

                            if (cachedPeriodInt > maxAllowedPeriodInt)
                                cachedPeriodInt = maxAllowedPeriodInt;

                            if (cachedPeriodInt < 1)
                                cachedPeriodInt = 1;

                            defaultPeriod = cachedPeriodInt;
                        }

                    }

                    this.selectedPeriod = defaultPeriod;

                    this.initSlider();

                    this.dataTable.period.html(defaultPeriod);
                    this.updateData(this.calculateInstallment(this.price, defaultPeriod));

                    this.privacyPolicyLink.bind('click', function (event) {
                        event.preventDefault();
                        var w = window.open($(this).attr('data-url'), 'privacy', 'menubar=0,status=0,toolbar=0,scrollbars=1');
                        w.focus();
                    });

                    this.termsLink.bind('click', function (event) {
                        event.preventDefault();
                        var w = window.open($(this).attr('data-url'), 'terms', 'menubar=0,status=0,toolbar=0,scrollbars=1');
                        w.focus();
                    });
                }, this));


                this.initialized = true;
            }
        },

        setPrice: function(price) {

            this.price = price;

            if (!this.initialized) {

                this.init();

            } else {
                this.slider.slider('float', {labels: this.pipValues()});
                this.updateData(this.calculateInstallment(this.price, this.selectedPeriod));
            }
        }

    };

    installmentSlider.decimalSeparator = sessionCurrency.dec;
    installmentSlider.thousandSeparator = sessionCurrency.thousand;
    installmentSlider.currencySymbol = sessionCurrency.sign;
    installmentSlider.decimalPlaces = sessionCurrency.decimal;
    installmentSlider.format = sessionCurrency.side != undefined ? '%s %v' : '%v %s';
});
