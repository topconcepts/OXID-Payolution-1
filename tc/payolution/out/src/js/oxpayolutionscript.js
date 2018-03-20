payolution_jQuery(function($){

	oxPayolutionInputValidator = {
            options: {
                classValid                   : "oxValid",
                classInValid                 : "oxInValid",
                errorParagraph               : "p.oxValidateError",
                methodValidate               : "js-oxPayolutionValidate",
                methodValidatePhone          : "js-oxPayolutionValidate_phone",
                methodValidateSsn            : "js-oxPayolutionValidate_ssn",
                methodValidateDate           : "js-oxPayolutionValidate_birthday",
                methodValidateInlineDate     : "js-oxPayolutionValidate_inlineBirthday",
                methodValidateAgree          : "js-oxPayolutionValidate_agree",
                methodAllowEmpty             : "js-oxPayolutionValidate_allowEmpty",
                errorMessageNotEmpty         : "js-oxPayolutionError_notEmpty",
                errorMessagePhoneFormat      : "js-oxPayolutionError_phoneFormat",
                errorMessageSsnFormat        : "js-oxPayolutionError_ssnFormat",
                errorMessageBirthdayAllFields: "js-oxPayolutionError_birthdayAllFields",
                errorMessageBirthdayIncorrect: "js-oxPayolutionError_birthdayIncorect",
                errorMessageAgree            : "js-oxPayolutionError_agree",
                listItem                     : "li",
                list                         : "ul",
                paragraph                    : "p",
                span                         : "span",
                form                         : "form",
                visible                      : ":visible"
            },

            _create: function() {

                var self    = this,
                    options = self.options,
                    el      = self.element;

                el.delegate("."+options.methodValidate, "blur", function() {

                	var oTrigger = this;
                    // the element who caused the event
                    // adding a timeout to delay the callback from modifying the form
                    // this allows other events like CLICK to be called before the blur event
                    // this happens only on some browsers where blur has higher priority than click
                    setTimeout(function(){
                        if ( $( oTrigger ).is(options.visible) ) {
                            var oFieldSet = self.getFieldSet( oTrigger );
                            if ( oFieldSet.children( '.'+options.methodValidateDate ).length <= 0 && oFieldSet.children( '.'+options.methodValidateAgree ).length <= 0) {
	                            var blIsValid = self.isFieldSetValid( oFieldSet, true );
	                            self.hideErrorMessage( oFieldSet );
	                            if ( blIsValid != true ){
	                                self.showErrorMessage( oFieldSet, blIsValid );
	                            }
                            }
                        }
                    }, 50);
                });

                el.delegate("." + options.methodValidateDate, "change", function () {
                    var fieldSet = self.getFieldSet(this);
                    var isValid = self.isFieldSetValid(fieldSet, true);
                    self.setDefaultState(fieldSet);

                    if (isValid !== true) {
                        self.showErrorMessage(fieldSet, isValid);
                    } else {
                        self.setDefaultState(fieldSet);
                    }
                });

                el.delegate("."+options.methodValidate, "click", function() {

                	var oTrigger = this;
                    // the element who caused the event
                    // adding a timeout to delay the callback from modifying the form
                    // this allows other events like CLICK to be called before the blur event
                    // this happens only on some browsers where blur has higher priority than click
                    setTimeout(function(){
                        if ( $( oTrigger ).is(options.visible) ) {
                            var oFieldSet = self.getFieldSet( oTrigger );
                            if ( oFieldSet.children( '.'+options.methodValidateAgree ).length >= 1 ) {
	                            var blIsValid = self.isFieldSetValid( oFieldSet, true );
	                            self.hideErrorMessage( oFieldSet );
	                            if ( blIsValid != true ){
	                                self.showErrorMessage( oFieldSet, blIsValid );
	                            }
                            }
                        }
                    }, 50);
                });

                el.bind( "submit", function() {
                    return self.submitValidation(this);
                });

                // Birthday
                el.find('.birthday-value').each(function (i, item) {

                    var element = $(item); // <- hidden input element with birthday value

                    var value = element.val();

                    var year = $(element).parent().children('.oxPayolutionYear');
                    var month = $(element).parent().children('.oxPayolutionMonth');
                    var day = $(element).parent().children('.oxPayolutionDay');

                    // *) select year, month, day selectbox to current value.

                    if (value) {
                        var parts = value.split('-');

                        year.val(parseInt(parts[0]));
                        month.val(parseInt(parts[1]));
                        day.val(parseInt(parts[2]));
                    }

                    function update()
                    {
                        var y = parseInt(year.val());
                        var m = parseInt(month.val());
                        var d = parseInt(day.val());

                        var datetime = '';

                        if (!isNaN(y) && !isNaN(m) && !isNaN(d)) {

                            datetime += y;
                            datetime += '-' + (m < 10 ? '0' : '') + m;
                            datetime += '-' + (d < 10 ? '0' : '') + d;

                        }

                        element.val(datetime);
                    }

                    // *) update value then selectboxes will be changed
                    year.bind('change', update);
                    month.bind('change', update);
                    day.bind('change', update);

                });

                $('.payoliution-flow-error-symbol').css('height', $('.payoliution-flow-error-text').css('height'));

            },

            /**
             * Validate form element, return forms true - valid, false - not valid
             *
             * @return boolean
             */
            inputValidation: function( oInput, blCanSetDefaultState )
            {
                var oOptions = this.options;
                var self = this;
                var blValidInput = true;

                var sValue = $(oInput).val();

                // Required field
                if ( $( oInput ).closest('.payolution-select-payment-element').hasClass('required-field') && blValidInput ) {

                    var sFilteredValue = sValue.replace(/^\s+|\s+$/g, '');

                    if (!sFilteredValue ){
                        return oOptions.errorMessageNotEmpty;
                    }
                }

                // Phone numer
                if ( $( oInput ).hasClass( oOptions.methodValidatePhone ) && blValidInput ) {

                	var sFilteredValue = sValue.replace(/[\ \-\/\(\)]/g, '');

                    if (!sFilteredValue ){
                        return oOptions.errorMessageNotEmpty;
                    }

                	var pattern = new RegExp(/^\+?[0-9]{8,18}$/);
                	if (!pattern.test(sFilteredValue)) {
                		return oOptions.errorMessagePhoneFormat;
                	}

                	if (sValue != sFilteredValue) {
                		$(oInput).val(sFilteredValue);
                	}
                }

                // SSN
                if ( $( oInput ).hasClass( oOptions.methodValidateSsn ) && blValidInput ) {

                    if (!$.trim(sValue) ){
                        return oOptions.errorMessageNotEmpty;
                    }

                	if (oxGlobalPayolutionPnoSsnEncoding) {  // oxGlobalPayolutionPnoSsnEncoding is defined in select_payment block
                		var pattern = false;
                		switch (oxGlobalPayolutionPnoSsnEncoding) {
	                		case '2': pattern = new RegExp(/^[0-9]{6,6}(([0-9]{2,2}[-\+]{1,1}[0-9]{4,4})|([-\+]{1,1}[0-9]{4,4})|([0-9]{4,6}))$/); break;
	                		case '3': pattern = new RegExp(/^[0-9]{6,6}((-[0-9]{5,5})|([0-9]{2,2}((-[0-9]{5,5})|([0-9]{1,1})|([0-9]{3,3})|([0-9]{5,5))))$/); break;
	                		case '4': pattern = new RegExp(/^[0-9]{6,6}(([A\+-]{1,1}[0-9]{3,3}[0-9A-FHJK-NPR-Y]{1,1})|([0-9]{3,3}[0-9A-FHJK-NPR-Y]{1,1})|([0-9]{1,1}-{0,1}[0-9A-FHJK-NPR-Y]{1,1}))$/); break;
	                		case '5': pattern = new RegExp(/^[0-9]{8,8}([0-9]{2,2})?$/); break;
                		}

                		if (pattern && !pattern.test(sValue)) {
                			return oOptions.errorMessageSsnFormat;
                		}
                	}
                }

                // Birthday
                if ( $( oInput ).hasClass( oOptions.methodValidateDate ) ) {
                    oDay   = $( oInput ).parent().children( '.oxPayolutionDay' );
                    oMonth = $( oInput ).parent().children( '.oxPayolutionMonth' );
                    oYear  = $( oInput ).parent().children( '.oxPayolutionYear' );

                    if (!oDay.val() && !oMonth.val() && !oYear.val()) {
                        if( !$( oInput ).hasClass( oOptions.methodAllowEmpty) ) {
                            return oOptions.errorMessageNotEmpty;
                        }
                    }
                    else if (!oDay.val() || !oMonth.val() || !oYear.val()) {
                    	return oOptions.errorMessageBirthdayAllFields;
                    }
                    else if ( oDay.val() && oMonth.val() && oYear.val() ) {
                        RE = /^\d+$/;
                        blDayOnlyDigits  = RE.test( oDay.val() );
                        blYearOnlyDigits = RE.test( oYear.val() );
                        if ( !blDayOnlyDigits || !blYearOnlyDigits ) {
                            return oOptions.errorMessageBirthdayIncorrect;
                        } else {
                            iMonthDays = new Date((new Date(oYear.val(), oMonth.val(), 1))-1).getDate();

                            if ( oDay.val() <= 0 || oYear.val() <= 0 || oDay.val() > iMonthDays ) {
                                return oOptions.errorMessageBirthdayIncorrect;
                            }

                            if (!self.isAdult(oYear.val(), oMonth.val(), oDay.val())) {
                                return oOptions.errorMessageBirthdayIncorrect;
                            }
                        }
                    }
                }
                
                //inline Birthday
                if ($( oInput ).hasClass( oOptions.methodValidateInlineDate )) {
                    var dateParts = $(oInput).val().split('.'),
                        date = dateParts[0],
                        month = dateParts[1] ? dateParts[1] : '',
                        year = dateParts[2] ? dateParts[2] : '',
                        dateCheck = /^(0?[1-9]|[12][0-9]|3[01])$/,
                        monthCheck = /^(0[1-9]|1[0-2])$/,
                        yearCheck = /^\d{4}$/;

                    if (month.match(monthCheck) && date.match(dateCheck) && year.match(yearCheck)) {
                        var daysList = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];

                        if (month == 1 || month > 2) {
                            if (date > daysList[month - 1]) {
                                return oOptions.errorMessageBirthdayIncorrect;
                            }
                        }

                        if (month == 2) {
                            var leapYear = false;
                            if ((!(year % 4) && year % 100) || !(year % 400)) {
                                leapYear = true;
                            }

                            if ((leapYear == false) && (date >= 29)) {
                                return oOptions.errorMessageBirthdayIncorrect;
                            }

                            if ((leapYear == true) && (date > 29)) {
                                return oOptions.errorMessageBirthdayIncorrect;
                            }

                        }

                        if (!self.isAdult(year, month, date)) {
                            return oOptions.errorMessageBirthdayIncorrect;
                        }
                    } else {
                        return oOptions.errorMessageBirthdayIncorrect;
                    }
                }

                if ( $( oInput ).hasClass("field-iban") && blValidInput ) {

                    var v = $( oInput ).val();

                    //This function check if the checksum if correct
                    v = v.replace(/^(.{4})(.*)$/,"$2$1"); //Move the first 4 chars from left to the right
                    v = v.replace(/[A-Z]/g,function(e){return e.charCodeAt(0) - 'A'.charCodeAt(0) + 10}); //Convert A-Z to 10-25
                    var sum = 0;
                    var ei = 1; //First exponent
                    for(var i = v.length - 1; i >= 0; i--){
                        sum += ei * parseInt(v.charAt(i),10); //multiply the digit by it's exponent
                        ei = (ei * 10) % 97; //compute next base 10 exponent  in modulus 97
                    };
                    var isValid = (sum % 97 == 1);

                    return isValid ? true : oOptions.errorMessageNotEmpty;
                }

                if ( $( oInput ).hasClass("field-bic") && blValidInput ) {
                    var bicPattern = /([a-zA-Z]{4}[a-zA-Z]{2}[a-zA-Z0-9]{2}([a-zA-Z0-9]{3})?)/;
                    var isValid = bicPattern.test($( oInput ).val());
                    return isValid ? true : oOptions.errorMessageNotEmpty;
                }

                // Consent
                if ( $( oInput ).hasClass( oOptions.methodValidateAgree ) && blValidInput ) {
                    if (! $(oInput).is(':checked')){
                        return oOptions.errorMessageAgree;
                    }
                }

                if ( $( oInput ).hasClass( oOptions.methodValidate ) && blCanSetDefaultState) {

                    if( !$( oInput ).val()){
                        self.setDefaultState( oInput );
                        return true;
                    }
                }

                return blValidInput;
            },

            /**
             *
             * @param year
             * @param month
             * @param day
             * @returns {boolean}
             */
            isAdult : function (year, month, day) {
                var age = 18,
                    mydate = new Date(),
                    minBirthdayDate = new Date();

                mydate.setFullYear(year, month - 1, day);
                minBirthdayDate.setFullYear(minBirthdayDate.getFullYear() - age);

                minBirthdayDate.setHours(0, 0, 0, 0);
                mydate.setHours(0, 0, 0, 0);
                return minBirthdayDate > mydate;
            },

            /**
             * On submit validate required form elements,
             * return true - if all filled correctly, false - if not
             *
             * @return boolean
             */
            submitValidation: function( oForm )
            {
                var blValid = true;
                var oFirstNotValidElement = null;
                var self = this;
                var oOptions = this.options;

                $( "." + oOptions.methodValidate, oForm).each(    function(index) {

                    if ( $( this ).is(oOptions.visible) ) {

                        var oFieldSet = self.getFieldSet(this);
                        self.hideErrorMessage( oFieldSet );
                        var blIsValid = self.isFieldSetValid( oFieldSet, false );
                        if ( blIsValid != true ){
                            self.showInputFields( oFieldSet, blIsValid );
                            self.showErrorMessage( oFieldSet, blIsValid );
                            blValid = false;
                            if( oFirstNotValidElement == null ) {
                                oFirstNotValidElement = this;
                            }
                        }
                    }
                });

                if( oFirstNotValidElement != null ) {
                    $( oFirstNotValidElement ).focus();
                }

                return blValid;
            },

            isFieldSetValid: function ( oFieldSet, blCanSetDefaultState ) {

                var blIsValid = true;
                var self = this;
                var oOptions = this.options;

                $( "." + oOptions.methodValidate, oFieldSet).each( function(index) {

                    if ( $( this ).is(oOptions.visible) ) {
                        var tmpblIsValid = self.inputValidation( this, blCanSetDefaultState );

                        if( tmpblIsValid != true){
                            blIsValid = tmpblIsValid;
                        }
                    }
                });

                return blIsValid;
            },

            /**
             * returns li element
             *
             *
             * @return object
             */
            getFieldSet: function( oField ){

               var oFieldSet =  $( oField ).parent();

               return oFieldSet;
            },

            /**
             * Show error messages
             *
             * @return object
             */
            showErrorMessage: function ( oObject, messageType )
            {
                if (!oObject.hasClass('payolution-select-payment-element')) {
                    oObject = $(oObject).closest('.payolution-select-payment-element');
                }
                oObject.removeClass(this.options.classValid);
                oObject.addClass(this.options.classInValid);
                oObject.find(this.options.errorParagraph).find( this.options.span + "." + messageType ).show().css("display", "inline-block");
                oObject.find(this.options.errorParagraph).show().css("display", "inline-block");

                return oObject;
            },

            /**
             * Shows all hidden input fields and hides all user text data
             *
             */
            showInputFields: function( oObject, messageType )
            {
                if($.trim(messageType) != $.trim(this.options.errorMessageAgree)){
                    var context = $(oObject).closest('.oxPayolutionPayment');
                    $('.payolutionHidden', context).removeClass('payolutionHidden');
                    $('.payolutionToHide', context).hide();
                }
            },

            /**
             * Hide error messages
             *
             * @return object
             */
            hideErrorMessage: function ( oObject )
            {
                this.hideMatchMessages( oObject );

                oObject.removeClass(this.options.classInValid);
                oObject.addClass(this.options.classValid);
                oObject.children(this.options.errorParagraph).children( this.options.span ).hide();
                oObject.children(this.options.errorParagraph).hide();

                return oObject;
            },

            /**
             * has match error messages
             *
             * @return boolean
             */
            hasOpenMatchMessage: function ( oObject )
            {
                return $( '.'+this.options.errorMessageNotEqual, oObject ).is( this.options.visible )
            },

            /**
             * Hide match error messages
             *
             * @return object
             */
            hideMatchMessages: function ( oObject )
            {
                if ( this.hasOpenMatchMessage( oObject.next(this.options.listItem) ) ){
                    this.hideErrorMessage( oObject.next(this.options.listItem) );
                }

                if ( this.hasOpenMatchMessage( oObject.prev(this.options.listItem) ) ){
                    this.hideErrorMessage( oObject.prev(this.options.listItem) );
                }
            },

            /**
             * Set default look of form list element
             *
             * @return object
             */
            setDefaultState: function ( oObject )
            {
                var oObject = $( oObject ).parent();

                oObject.removeClass(this.options.classInValid);
                oObject.removeClass(this.options.classValid);
                oObject.children(this.options.errorParagraph).hide();

                oOptions = this.options;

                $( this.options.span, oObject.children( this.options.errorParagraph ) ).each( function(index) {
                    oObject.children( oOptions.errorParagraph ).children( oOptions.span ).hide();
                });

                return oObject;
            }

        };

    /**
     * Form Items validator
     */
    $.widget("ui.oxPayolutionInputValidator", oxPayolutionInputValidator );

    /**
     * Field help
     */
    $('.payo-help').bind('click', function () {
        $(this).next('.payo-help-text').toggle();
    });
});

var b2bCompanyFormType = function () {
    var self = {
        b2bCompanyTypeInput : $('[name="dynvalue[payolution_b2b_type]"]'),
        b2bCompanyNameInput : $('[name="dynvalue[payolution_b2b_name]"]'),
        b2bCompanyTypeMap : [],
        b2bCompanyNamesMap : [],
        b2bVisibleFormInputMap : []
    };

    self.init = function () {
        self.initMaps();
        $(document).ready(function () {
            self.bindEvents();
        });

        self.changeFormFieldVisibility(self.b2bCompanyTypeInput.val());
    };

    self.bindEvents = function () {
        if (self.b2bCompanyNameInput.length > 0)
        {
            self.b2bCompanyTypeInput.bind('change', function () {
                self.changeFormFieldVisibility($(this).val());
            });

            self.b2bCompanyTypeInput.closest('.selectpicker').bind('changed.bs.select', function (e, clickedIndex, newValue, oldValue) {
                self.changeFormFieldVisibility(newValue);
            });
        }
    };

    self.initMaps = function () {
        var options = self.b2bCompanyTypeInput.data('options');
        if (options) {
            self.b2bCompanyTypeMap = options['typeMap'];
            self.b2bCompanyNamesMap = options['companyNames'];
            self.formatVisibleFormNameMap();
        }
    };

    self.formatVisibleFormNameMap = function () {
        for (var companyType in self.b2bCompanyTypeMap) {
            self.b2bVisibleFormInputMap[companyType] = [];
            for (var i = 0; i < self.b2bCompanyTypeMap[companyType].length; i++) {
                self.b2bVisibleFormInputMap[companyType].push("dynvalue[payolution_b2b_" + self.b2bCompanyTypeMap[companyType][i] + "]");
            }
        }
    };

    /**
     *
     * @param selectedType
     */
    self.changeFormFieldVisibility = function (selectedType) {
        var visibleFields = self.b2bVisibleFormInputMap[selectedType],
            elements = $('[name^="dynvalue[payolution_b2b_"]');

        self.changeCompanyNameLabel(selectedType);
        for (var i = 0; i < elements.length; i++) {
            var $element = $(elements[i]),
                elementContainer = $element.closest('.payolution-select-payment-element'),
                name = $element.attr('name');

            if (visibleFields.indexOf(name) === -1) {
                elementContainer.hide();
                elementContainer.removeClass('required-field');
            } else {
                elementContainer.show();
                elementContainer.addClass('required-field');
            }
        }
    };

    /**
     *
     * @param companyType
     */
    self.changeCompanyNameLabel = function (companyType) {
        self.b2bCompanyNameInput.closest('.payolution-select-payment-element').find('label').text(self.b2bCompanyNamesMap[companyType]);
    };

    return self;
};

b2bCompanyFormType().init();
