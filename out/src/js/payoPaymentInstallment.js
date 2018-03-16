var installmentController;
payolution_jQuery(function ($) {
    installmentController = {
        // Dependencies
        dependencies       : {
            installmentSlider: null
        },

        // Flags
        initialized        : false,
        isGermany          : false,

        // Elements containers
        tabsContainer      : null,
        allTabs            : null,
        allPanels          : null,

        // Tab elements
        tabs               : {
            personalInfo     : null,
            installmentPeriod: null,
            bankInfo         : null,
            last             : null
        },

        // Tab panels
        panels             : {
            personalInfo     : null,
            installmentPeriod: null,
            bankInfo         : null
        },

        // Buttons
        buttons            : {
            toInstallmentTab: null,
            toBankInfoTab   : null
        },

        // Button to next order step
        nextOrderStepButton: null,

        terms  : null,
        privacy: null,

        /**
         * Update tabs according to user info
         */
        updateTabs: function () {
            // Bank info tab only required in Denmark
            if (!this.isGermany) {
                this.tabs.bankInfo.remove();
                this.panels.bankInfo.remove();
                this.tabs.installmentPeriod.addClass('defaultLast');
                this.tabs.personalInfo.addClass('payo-tab-personalInfo-large');
                this.tabs.installmentPeriod.addClass('payo-tab-personalInfo-large');
                this.tabsContainer.width(this.tabs.personalInfo.width() + 15 * 2);
            }
        },

        /**
         * Switch tab
         *
         * @param {string} tab
         */
        openTab: function (tab) {
            this.hideErrorMessages();
            switch (tab) {
                case 'personalInfo':
                    this.openTabPersonalInfo();
                    break;
                case 'installmentPeriod':
                    this.openTabInstallmentPeriod();
                    break;
                case 'bankInfo':
                    this.openTabBankInfo();
                    break;
                default:
                    break;
            }
        },

        /**
         * Hide error messages
         */
        hideErrorMessages: function () {
            $('#content .status.error').hide();
        },

        /**
         * Switch tab only if it was opened earlier
         *
         * @param {string} tab
         */
        openPassedTab: function (tab) {
            switch (tab) {
                case 'personalInfo':
                    this.openTabPersonalInfoIfPassed();
                    break;
                case 'installmentPeriod':
                    this.openTabInstallmentPeriodIfPassed();
                    break;
                case 'bankInfo':
                    this.openTabBankInfoIfPassed();
                    break;
                default:
                    break;
            }
        },

        /**
         * Open personal info tab only if it was opened earlier
         */
        openTabPersonalInfoIfPassed: function () {
            if (!this.tabs.personalInfo.hasClass('passed')) {
                return;
            }

            this.openTabPersonalInfo();
        },


        /**
         * Open installment period tab only if it was opened earlier
         */
        openTabInstallmentPeriodIfPassed: function () {
            if(this.buttons.toInstallmentTab.is(':enabled')) {
                this.buttons.toInstallmentTab.click();
            }
        },

        /**
         * Open bank info tab only if it was opened earlier
         */
        openTabBankInfoIfPassed: function () {
            if(this.buttons.toBankInfoTab.is(':enabled')) {
                this.buttons.toBankInfoTab.click();
            }
        },

        /**
         * Validate user age. Return true if user is 18 years old or more
         */
        validateTabPersonalInfoAge: function () {
            var day = $("#dob_day").val();
            var month = $("#dob_month").val();
            var year = $("#dob_year").val();
            var age = 18;

            var mydate = new Date();
            mydate.setFullYear(year, month - 1, day);

            var currdate = new Date();
            currdate.setFullYear(currdate.getFullYear() - age);

            return currdate > mydate;
        },

        /**
         * Open personal info tab
         */
        openTabPersonalInfo: function () {
            this.allTabs.removeClass('active passed activeLast');
            this.allPanels.hide();

            this.tabs.personalInfo.addClass('active');
            this.tabs.last.addClass('defaultLast');
            this.panels.personalInfo.show();
        },

        /**
         * Open installment period chose tab
         */
        openTabInstallmentPeriod: function () {
            this.dependencies.installmentSlider.useServerSide = true; // <- for Checkout enable server side (payolution CL)
            this.dependencies.installmentSlider.init();
            this.allTabs.removeClass('active passed activeLast');
            this.allPanels.hide();

            this.tabs.personalInfo.addClass('passed');

            if (this.isGermany) {
                this.tabs.last.addClass('defaultLast');
                this.tabs.installmentPeriod.addClass('active');
            } else {
                this.tabs.installmentPeriod.removeClass('defaultLast');
                this.tabs.installmentPeriod.addClass('activeLast active');
            }
            this.panels.installmentPeriod.show('slide', function () {
                installmentController.updatePdfLink();
            });
        },

        /**
         * Open bank info tab
         */
        openTabBankInfo: function () {
            this.allTabs.removeClass('active passed activeLast');
            this.allPanels.hide();

            this.tabs.personalInfo.addClass('passed');
            this.tabs.installmentPeriod.addClass('passed');
            this.tabs.bankInfo.removeClass('defaultLast');
            this.tabs.bankInfo.addClass('activeLast active');
            this.panels.bankInfo.show();
        },

        checkInstallmentAvailability: function ()
        {
            var controller  = 'order';
            var action      = 'check_installment';

            var url = 'index.php?cl='+controller+'&fnc='+action;

            var promise = $.Deferred();

            function formValues() {
                var fields = {};

                $.each($('#payment,form[name="order"]').serializeArray(), function (i, field) {
                    var matches = new RegExp('^dynvalue\\[(payolution_installment_.+)\\]').exec(field.name);
                    if (matches)
                        fields[matches[1]] = field.value;
                });

                return fields;
            }

            $.post(url,
                {
                    cl:    controller,
                    fnc:   action,
                    values: formValues()
                },
                $.proxy(function (response)
                {
                    if (response && response.status == 'ok') {
                        promise.resolve();
                    } else if (response && response.status == 'error') {
                        if (response.html !== undefined) promise.reject({html: response.html});
                        else promise.reject({message: 'Server Error: ' + response.message});
                    } else {
                        promise.reject({message: 'Got error from server: ' + JSON.stringify(response)});
                    }
                }, this),
                'json'
            ).fail(function (response)
                {
                    promise.reject(response);
                });

            return promise;
        },

        withLoadingIndicator: function(promise) {
            var button = this.buttons.toBankInfoTab;
            var loader;

            function showLoader() {
                loader = $('.payo-check-installment-loader');
                if (loader.size() <= 0) {
                    loader = $('<div class="payo-check-installment-loader payo-loader"><div></div></div>');
                    button.after(loader);
                }
                loader.fadeTo(0, 1).show();
            }

            function hideLoader() {
                if (loader) {
                    loader.fadeTo('fast', 0, function () {
                        $(this).hide();
                        loader = null;
                    });
                }
            }

            showLoader();
            promise.always(hideLoader);
        },

        /**
         * Bind event listeners
         */
        bindEvents: function () {
            var $this = this;

            // Click on tab to switch
            this.allTabs.bind('click', function () {
                $this.openPassedTab($(this).data('tab'));
            });

            this.buttons.toInstallmentTab.bind('click', function () {
                
                var form = $('#payo-panel-personalInfo .form');

                if (oxPayolutionInputValidator.submitValidation(form)) {
                    $this.openTab('installmentPeriod');
                }
            });

            this.buttons.toBankInfoTab.bind('click', function () {
                var form = $('#payo-panel-installmentPeriod .form');
                if (oxPayolutionInputValidator.submitValidation(form)) {
                    $this.withLoadingIndicator(
                        $this.checkInstallmentAvailability().done(function () {
                            $this.openTab('bankInfo');
                        }).fail(function (response) {

                            function showError(html) {
                                var errorContainer = $('.payo-error-container');

                                if (errorContainer.size() <= 0) {
                                    errorContainer = $('<div class="payo-error-container alert alert-danger"></div>');

                                    var from = $('#payment,form[name="order"]');
                                    from.before(errorContainer);
                                }
                                errorContainer.empty().html(html);
                            }

                            if (response.html !== undefined)
                                showError(response.html);

                            if (response.message !== undefined)
                                alert(response.message);
                        })
                    );
                }
            });

            this.nextOrderStepButton.bind('click', function() {
                if($('#payment_payolution_installment').is(':checked')) {
                    var tab = $('#payo-installment-tabs').find('.active').data('tab');
                    if(tab == 'personalInfo') {
                        $this.buttons.toInstallmentTab.click();
                        return false;
                    }

                    if(tab == 'installmentPeriod' && $this.isGermany) {
                        $this.buttons.toBankInfoTab.click();
                        return false;
                    }
                }
            });
        },

        /**
         * Init controller
         *
         * @param {boolean} isGermany
         * @param {{installmentSlider:{initialized: boolean, url: {CLRequest: string}, init: Function}}}  dependencies
         */
        init: function (isGermany, dependencies) {
            if (!this.initialized) {
                this.nextOrderStepButton = $('#paymentNextStepBottom,#test_PaymentNextStepBottom');
                this.isGermany = isGermany;
                this.dependencies = $.extend({}, this.dependencies, dependencies);

                // Top level
                this.tabsContainer = $('#payo-installment-tabs');
                this.allTabs = this.tabsContainer.find('li');
                this.allPanels = $('.payo-installment-panel');

                // Tabs
                this.tabs.personalInfo = $('#payo-tab-personalInfo');
                this.tabs.installmentPeriod = $('#payo-tab-installmentPeriod');
                this.tabs.bankInfo = $('#payo-tab-bankInfo');
                this.tabs.last = this.isGermany ? this.tabs.bankInfo : this.tabs.installmentPeriod;

                // Panels
                this.panels.personalInfo = $('#payo-panel-personalInfo');
                this.panels.installmentPeriod = $('#payo-panel-installmentPeriod');
                this.panels.bankInfo = $('#payo-panel-bankInfo');

                // Buttons
                this.buttons.toInstallmentTab = $('#payo-button-toInstallmentPeriod');
                this.buttons.toBankInfoTab = $('#payo-button-toBankInfo');
                this.privacy = $('input[name="dynvalue[payolution_installment_privacy]"]');

                // Init
                this.bindEvents();
                this.openTabPersonalInfo();
                this.updateTabs();

                // Update installment sum to match currency formatting rules from OXID
                var sum = $('#payo-installment-sum');
                sum.html(installmentSlider.formatCurrency(parseFloat(sum.html())));

                // Set initialized flag
                this.initialized = true;
            }
        },
        updatePdfLink : function () {
            var base64cmp = $('#base64cmp').val(),
                activeLanguageAbbreviationParameter = (typeof activeLanguageAbbreviation !== 'undefined') ? ('&lang='+activeLanguageAbbreviation) : '',
                activeBillingCountryParameter = (typeof activeBillingCountry !== 'undefined') ? ('territory='+activeBillingCountry) : '',
                infoPortLink = 'https://payment.payolution.com/payolution-payment/infoport/dataprivacydeclaration?' +
                    activeBillingCountryParameter + activeLanguageAbbreviationParameter + '&mId=' + base64cmp;

            $('#privacyPdf').attr('data-url', infoPortLink);
        }
    };

    $(document).ready(function(){
        var button = $('#paymentNextStepBottom,#test_PaymentNextStepBottom'),
            disable = $('#payoDisableNextStep').val() == 1,
            activeLanguageAbbreviationParameter = (typeof activeLanguageAbbreviation !== 'undefined') ? ('&lang='+activeLanguageAbbreviation) : '',
            activeBillingCountryParameter = (typeof activeBillingCountry !== 'undefined') ? ('territory='+activeBillingCountry) : '';

        // Init when installment payment is selected
        $('input[type=radio][name=paymentid]').bind('change', function () {
            var val = this.value;

            if (!disable && val == 'payolution_installment') { // Installment payment
                installmentController.init($('#payo-user-country').val(), {installmentSlider: installmentSlider});
            }
        });

        $('input[type=radio][name=paymentid]:checked').change();

        $('input[name="dynvalue[payolution_b2c_privacy]"]').bind('change', function () {
            if ($('input[name="dynvalue[payolution_b2c_privacy]"]:checkbox').prop('checked')) {
                button.removeAttr('disabled');
            } else {
                button.attr('disabled', 'disabled');
            }
        });

        $('input[name="dynvalue[payolution_dd_privacy]"], input[name="dynvalue[payolution_dd_mandate]"]').bind('change', function () {
            if ($('input[name="dynvalue[payolution_dd_privacy]"]:checkbox').prop('checked')&& $('input[name="dynvalue[payolution_dd_mandate]"]:checkbox').prop('checked')) {
                button.removeAttr('disabled');
            } else {
                button.attr('disabled', 'disabled');
            }
        });

        var base64cmp = $('#base64cmp').val(),
            infoPortLink = 'https://payment.payolution.com/payolution-payment/infoport/dataprivacydeclaration?' +
                activeBillingCountryParameter + activeLanguageAbbreviationParameter + '&mId=' + base64cmp;
        $('#privacyPdf').attr('data-url', infoPortLink);
        
        $('#privacyPdfb2c').attr('data-url', infoPortLink);
        $('#privacyPdfb2b').attr('data-url', infoPortLink);
        $('#privacyPdfDd').attr('data-url', infoPortLink);

        $('#mandatePdfDd').attr('data-url', 'https://payment.payolution.com/payolution-payment/infoport/sepa/mandate.pdf');

        $('#privacyPdfb2c, #privacyPdfb2b, #privacyPdfDd').bind('click', function (event) {
            event.preventDefault();
            var w = window.open($(this).attr('data-url'), 'pop', 'menubar=0,status=0,toolbar=0,scrollbars=1');
            w.focus();
        });

        $('#mandatePdfDd').bind('click', function (event) {
            event.preventDefault();
            window.location.assign($(this).attr('data-url'));
        });
    });
});
