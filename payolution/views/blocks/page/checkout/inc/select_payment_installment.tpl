[{if $paymentContext->isCountryViable() }]
    [{assign var="isCountryViable" value=1}]
[{else}]
    [{assign var="isCountryViable" value=''}]
[{/if}]
[{assign var="basket" value=$paymentContext->basket()}]
[{assign var="priceValue" value=$basket->totalOrderPrice()}]
[{assign var="pdfIcon" value=$oViewConf->getModuleUrl("payolution", "out/src/img/pdf.png")}]
<div id="payo-installment-wrapper">
    <div id="payo-loader"><div></div></div>
    [{if $oViewConf->isActiveThemeFlow()}]
    <ol id="payo-installment-tabs" class="checkoutSteps clear">
        <li class="step1 active" id="payo-tab-personalInfo" data-tab="personalInfo">
            <div class="num">1</div>
            <div class="text">[{oxmultilang ident=PAYO_PAYMENT_INSTALLMENT_TAB_PERSONAL_INFO}]</div>
        </li>
        <li class="step2" id="payo-tab-installmentPeriod" data-tab="installmentPeriod">
            <div class="num">2</div>
            <div class="text">[{oxmultilang ident=PAYO_PAYMENT_INSTALLMENT_TAB_PERIOD}]</div>
        </li>
        <li class="step3 defaultLast" id="payo-tab-bankInfo" data-tab="bankInfo">
            <div class="num">3</div>
            <div class="text">[{oxmultilang ident=PAYO_PAYMENT_INSTALLMENT_TAB_BANK}]</div>
        </li>
    </ol>
    [{else}]
    <ul id="payo-installment-tabs" class="checkoutSteps clear">
        <li class="step1 active" id="payo-tab-personalInfo" data-tab="personalInfo">
            <span>[{oxmultilang ident=PAYO_PAYMENT_INSTALLMENT_TAB_PERSONAL_INFO}]</span>
        </li>
        <li class="step2" id="payo-tab-installmentPeriod" data-tab="installmentPeriod">
            <span>[{oxmultilang ident=PAYO_PAYMENT_INSTALLMENT_TAB_PERIOD}]</span>
        </li>
        <li class="step3 defaultLast" id="payo-tab-bankInfo" data-tab="bankInfo">
            <span>[{oxmultilang ident=PAYO_PAYMENT_INSTALLMENT_TAB_BANK}]</span>
        </li>
    </ul>
    [{/if}]
    <input value="[{$isCountryViable}]" id="payo-user-country" type="hidden"/>
    <input value="[{$priceValue}]" id="payo-order-sum" type="hidden"/>
    <div id="payo-panel-personalInfo" class="payo-installment-panel">
        <br/>
        <h3>[{oxmultilang ident=PAYO_PAYMENT_INSTALLMENT_ENTER_DATE_AND_NUMBER}]</h3>
        <ul class="form payolutionUserDetails payolution_usertext_[{$sPaymentID}]">
            [{include file="inc/select_payment_element.tpl" element=$form->getElement('dynvalue[payolution_installment_birthday]')}]
        </ul>
        [{if $oViewConf->isActiveThemeFlow()}]
            <button id="payo-button-toInstallmentPeriod" type="button" class="btn btn-primary submitButton largeButton">
                [{oxmultilang ident=PAYO_PAYMENT_INSTALLMENT_BUTTON_TO_INSTALLMENT}]
                <i class="fa fa-caret-right"></i>
            </button>
        [{else}]
            <button id="payo-button-toInstallmentPeriod" type="button" class="submitButton largeButton btn btn-primary">
                [{oxmultilang ident=PAYO_PAYMENT_INSTALLMENT_BUTTON_TO_INSTALLMENT}]
            </button>
        [{/if}]
    </div>

    <div id="payo-panel-installmentPeriod" class="payo-installment-panel payoSlider">
        <br/>
        <h3>[{oxmultilang ident="PAYO_PAYMENT_INSTALLMENT_TITLE"}]</h3>

        <div id="installment-slider"></div>
        [{include file="inc/select_payment_element.tpl" element=$form->getElement('dynvalue[payolution_installment_period]')}]

        <div class="payo-installment-wrapper-left">
            <table id="payo-installment-data">
                <tr>
                    <th>[{oxmultilang ident=PAYO_PAYMENT_INSTALLMENT_SUM}]:</th>
                    <td id="payo-installment-sum">[{$priceValue}]</td>
                </tr>
                <tr>
                    <th>[{oxmultilang ident=PAYO_PAYMENT_INSTALLMENT_TOTAL_SUM}]:</th>
                    <td id="payo-installment-total-sum"></td>
                </tr>
                <tr>
                    <th>[{oxmultilang ident=PAYO_PAYMENT_INSTALLMENT_PERIOD}]:</th>
                    <td id="payo-installment-period"></td>
                </tr>

                <tr>
                    <th>[{oxmultilang ident=PAYO_PAYMENT_INSTALLMENT_INTEREST_RATE}]:</th>
                    <td id="payo-installment-interest-rate"></td>
                </tr>
                <tr>
                    <th>[{oxmultilang ident=PAYO_PAYMENT_INSTALLMENT_EFFECTIVE_INTEREST}]:</th>
                    <td id="payo-installment-effective-interest"></td>
                </tr>
                <tr>
                    <th>[{oxmultilang ident=PAYO_PAYMENT_INSTALLMENT_MONTHLY_SUM}]:</th>
                    <td id="payo-installment-monthly-sum"></td>
                </tr>
            </table>
        </div>
        <div class="payo-installment-wrapper-right">
            <a href="javascript:void(0)" class="readMore" id="payo-installment-details-toggler" data-show="[{oxmultilang ident=PAYO_PAYMENT_INSTALLMENT_SHOW_DETAILS}]" data-hide="[{oxmultilang ident=PAYO_PAYMENT_INSTALLMENT_HIDE_DETAILS}]">[{oxmultilang ident=PAYO_PAYMENT_INSTALLMENT_SHOW_DETAILS}]</a>
            <br/>
            <div id="payo-installment-details"></div>
            <a id="payo-pdf" href="javascript:void()" data-pdf-base-url='[{oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=PayolutionPdfDownload"}]' class="readMore" >[{oxmultilang ident="PAYOLUTION_INSTALLMENT_DRAFT_CREDIT_AGREEMENT"}]</a>
            <img src="[{$pdfIcon}]" alt="PDF" width="12" height="12"/>
        </div>
        <div style="clear:both"></div>

        <ul class="form payolutionUserDetails payolution_usertext_[{$sPaymentID}]" id="payoPrivacyPolicy">
            [{include file="inc/select_payment_element.tpl" element=$form->getElement('dynvalue[payolution_installment_privacy]')}]
        </ul>
        [{if $paymentContext->isCountryViable()}]
        [{if $oViewConf->isActiveThemeFlow()}]
        <button id="payo-button-toBankInfo" type="button" class="btn btn-primary submitButton largeButton">
            [{oxmultilang ident=PAYO_PAYMENT_INSTALLMENT_BUTTON_TO_BANKINFO}]
            <i class="fa fa-caret-right"></i>
        </button>
        [{else}]
            <button id="payo-button-toBankInfo" type="button" class="submitButton largeButton">[{oxmultilang ident=PAYO_PAYMENT_INSTALLMENT_BUTTON_TO_BANKINFO}]</button>
        [{/if}]
        [{/if}]
    </div>
    [{if $paymentContext->isCountryViable()}]
        <div id="payo-panel-bankInfo" class="payo-installment-panel">
            <ul class="form payolutionUserDetails payolution_usertext_[{$sPaymentID}]">
                [{include file="inc/select_payment_element.tpl" element=$form->getElement('dynvalue[payolution_installment_iban]')}]
                [{include file="inc/select_payment_element.tpl" element=$form->getElement('dynvalue[payolution_installment_account_holder]')}]
            </ul>
        </div>
    [{/if}]
</div>
