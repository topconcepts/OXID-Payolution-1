<?php
/**
 * Copyright 2015 Payolution GmbH
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0 [^]
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

$sLangName = "English";
// -------------------------------
// RESOURCE IDENTITFIER = STRING
// -------------------------------
$aLang = array(
    'charset'                                                    => 'ISO-8859-15',
    'EXCEPTION_PAYOLUTION_INPUT_DIFFERENT_ADDRESSES'             => 'Shipping and billing addresses must match',
    'EXCEPTION_PAYOLUTION_INPUT_INVALID_PHONE_FORMAT'            => 'Invalid phone numer',
    'EXCEPTION_PAYOLUTION_INPUT_NOTALLFIELDS'                    => 'Please fill out the field correctly!',
    'EXCEPTION_PAYOLUTION_INPUT_NO_BIRTHDAY_FIELD'               => 'Please specify day, month and year',
    'EXCEPTION_PAYOLUTION_INPUT_TERMS_AGREEMENT_MUST_BE_CHECKED' => 'You must agree with terms of agreement',
    'PAYOLUTION_HOLDER_DD'                                       => 'Holder',
    'PAYOLUTION_IBAN_DD'                                         => 'IBAN',
    'PAYOLUTION_BIC_DD'                                          => 'BIC',
    'PAYOLUTION_MANDATE_DD'                                      => 'I authorize the net-m private bank a <span id="mandatePdfDd" class="pdfLink">SEPA direct debit mandate</span>',
    'PAYOLUTION_ACCOUNT_HOLDER'                                  => 'Account holder:',
    'PAYOLUTION_BIC'                                             => 'BIC:',
    'PAYOLUTION_BIRTHDAY'                                        => 'Birthday:',
    'PAYOLUTION_TYPE'                                            => 'Form of organisation:',
    'PAYOLUTION_COMPANY_TYPE_PUBLIC'                             => 'Public',
    'PAYOLUTION_COMPANY_TYPE_REGISTERED'                         => 'Registered society',
    'PAYOLUTION_COMPANY_TYPE_SOLE'                               => 'Sole proprietorship',
    'PAYOLUTION_COMPANY_TYPE_COMPANY'                            => 'Company',
    'PAYOLUTION_COMPANY_TYPE_OTHER'                              => 'Other',
    'PAYOLUTION_COMPANY_NAME_BASE'                               => 'Company name:',
    'PAYOLUTION_COMPANY_NAME_PUBLIC'                             => 'Name of authority:',
    'PAYOLUTION_COMPANY_NAME_REGISTERED'                         => 'Name of society:',
    'PAYOLUTION_OWNER_FAMILY'                                    => 'Owner lastname:',
    'PAYOLUTION_OWNER_GIVEN'                                     => 'Owner firstname:',
    'PAYOLUTION_OWNER_BIRTHDAY'                                  => 'Owner birthdate:',
    'PAYOLUTION_BIRTHDAY_PATTERN'                                => 'dd.mm.yyyy',
    'PAYOLUTION_PHONE'                                           => 'Phone:',
    'PAYOLUTION_CONNECTION_ERROR_MESSAGE'                        => 'Connection to payolution server failed.',
    'PAYOLUTION_DUE'                                             => 'due',
    'PAYOLUTION_ERROR_ADDRESSES_IS_NOT_THE_SAME'                 => 'Shipping address mustn\'t differ from billing address.',
    'PAYOLUTION_ERROR_AGE_RESTRICTION'                           => 'You must be at least 18 years old.',
    'PAYOLUTION_ERROR_REMOTE_ERROR_RESPONSE'                     => 'This transaction could not be performed. This may have different causes, such as invalid user input, an unknown address or a temporary technical problem. Please validate your data or choose an alternative payment method.',
    'PAYOLUTION_ERROR_TERMS_AND_CONDITIONS_HAS_NOT_AGREED'       => 'Please accept the terms and conditions.',
    'PAYOLUTION_ERROR_DATA_PRIVACY'                              => 'You must agree with the transmission of data.',
    'PAYOLUTION_FROM'                                            => 'from',
    'PAYOLUTION_HELP_BIRTHDAY'                                   => 'The date of birth, we need to account for the availability of %s to consider.',
    'PAYOLUTION_HELP_BIRTHDAY_B2B'                               => 'Please specify birthdate of ordering person (sole trader). We need the birthdate to check availability of %s.',
    'PAYOLUTION_HELP_IBAN'                                       => 'The monthly rates according to instalment plan will be debited from your bank account. You can withdraw your consent at any time.',
    'PAYOLUTION_HELP_PAYMENT_B2B'                                => 'Invoice for business customers',
    'PAYOLUTION_HELP_PAYMENT_B2C'                                => 'Invoice',
    'PAYOLUTION_HELP_PAYMENT_DD'                                 => 'Direct Debit',
    'PAYOLUTION_HELP_PAYMENT_INSTALLMENT'                        => 'Instalment',
    'PAYOLUTION_IBAN'                                            => 'IBAN:',
    'PAYOLUTION_INSTALLMENT_DRAFT_CREDIT_AGREEMENT'              => 'Download Draftcreditagreement.pdf',
    'PAYOLUTION_PAYMENT_PER_MONTH'                               => 'Month',
    'PAYOLUTION_PRIVACY_POLICY'                                  => 'By providing the necessary to complete the purchase invoice and an identity and credit check data to payolution I agree. My <span id="privacyPdf" class="pdfLink">consent</span> can be revoked at any time with effect for the future.',
    'PAYOLUTION_PRIVACY_POLICY_B2C'                              => 'By providing the necessary to complete the purchase invoice and an identity and credit check data to payolution I agree. My <span id="privacyPdfDd" class="pdfLink">consent</span> can be revoked at any time with effect for the future.',
    'PAYOLUTION_PRIVACY_POLICY_DD'                               => 'By providing the necessary to complete the purchase debit and an identity and credit check data to payolution I agree. My <span id="privacyPdfb2c" class="pdfLink">consent</span> can be revoked at any time with effect for the future.',
    'PAYOLUTION_PRIVACY_POLICY_B2B'                              => 'By providing the necessary to complete the purchase invoice and an identity and credit check data to payolution I agree. My <span id="privacyPdfb2b" class="pdfLink">consent</span> can be revoked at any time with effect for the future.',
    'PAYOLUTION_READ_MORE'                                       => 'Example calculation',
    'PAYOLUTION_UST_ID'                                          => 'VAT-ID',
    'PAYO_INSTALLMENT_MODAL_DESCRIPTION'                         => '*) The rate values listed are examples only for the selected product amount / current transaction value. The final rates you will see during the buying process in the payment type selection.',
    'PAYO_INSTALLMENT_MODAL_TEXT'                                => 'Buy this product with comfortable rates - simply select the desired number of monthly installments:',
    'PAYO_INSTALLMENT_MODAL_TITLE'                               => 'Example instalment payment *',
    'PAYO_PAYMENT_FILL_DATA'                                     => 'Please complete the following data',
    'PAYO_PAYMENT_INSTALLMENT_BUTTON_TO_BANKINFO'                => 'Continue to bank info step',
    'PAYO_PAYMENT_INSTALLMENT_BUTTON_TO_INSTALLMENT'             => 'Continue to instalment step',
    'PAYO_PAYMENT_INSTALLMENT_EFFECTIVE_INTEREST'                => 'Effective interest',
    'PAYO_PAYMENT_INSTALLMENT_ENTER_DATE_AND_NUMBER'             => 'Enter birth date and phone number',
    'PAYO_PAYMENT_INSTALLMENT_HIDE_DETAILS'                      => 'Hide instalment plan',
    'PAYO_PAYMENT_INSTALLMENT_INTEREST_RATE'                     => 'Interest rate',
    'PAYO_PAYMENT_INSTALLMENT_MONTHLY_SUM'                       => 'Instalment amount',
    'PAYO_PAYMENT_INSTALLMENT_PERIOD'                            => 'Period',
    'PAYO_PAYMENT_INSTALLMENT_SHOW_DETAILS'                      => 'Show instalment plan',
    'PAYO_PAYMENT_INSTALLMENT_SUM'                               => 'Sum',
    'PAYO_PAYMENT_INSTALLMENT_TAB_BANK'                          => '3. Bank info',
    'PAYO_PAYMENT_INSTALLMENT_TAB_PERIOD'                        => '2. Instalment period',
    'PAYO_PAYMENT_INSTALLMENT_TAB_PERSONAL_INFO'                 => '1. Personal info',
    'PAYO_PAYMENT_INSTALLMENT_TITLE'                             => 'Please select the rate duration in months:',
    'PAYO_PAYMENT_INSTALLMENT_TOTAL_SUM'                         => 'Total sum',
    'PAYO_PAYMENT_INSTALLMENT_RATE'                              => 'Rate',
    'PAYOLUTION_ERROR_MESSAGE_INCORRECT_BIRTHDAY_DATE'           => 'You have to be at least of the age of 18 years to use this payment option',
    'PAYOLUTION_ERROR_REQUEST_WAS_REJECTED_ON_PRECHECK'          => 'This transaction could not be performed. This may have different causes, such as invalid user input, an unknown address or a temporary technical problem. Please validate your data or choose an alternative payment method.',
    'PAYOLUTION_ERROR_MISSING_REQUIRED_FIELDS'                   => 'Required fields are missing',
    'PAYOLUTION_ERROR_ORDER_STATUS_TRANSITION_NOT_ALLOWED'       => 'Order status transition is not allowed',
    'PAYOLUTION_MESSAGE_CONFIRMATION_NOT_SUCCEED'                => 'Unfortunately the e-mail for confirming your order couldn\'t be sent.',
    'PAYOLUTION_MESSAGE_WE_WILL_INFORM_YOU'                      => 'We will inform you immediately if an item is not deliverable.',
    'PAYOLUTION_MESSAGE_YOU_RECEIVED_ORDER_CONFIRM'              => 'You\'ve already received an e-mail with an order confirmation.',
    'PAYOLUTION_REGISTERED_YOUR_ORDER'                           => 'We registered your order under the number: %s ',
    'PAYOLUTION_THANK_YOU_FOR_ORDER'                             => 'Thank you for your order in',
    'PAYO_ORDER_INSTALLMENT_MONTHLY_SUM'                         => 'In case of instalment payment',
);
