[{assign var="oxPayolutionScript" value=$oViewConf->getPayolutionModuleUrl('out/src/js/oxpayolutionscript.js')}]

[{assign var="payolution" value=$oViewConf->getPayolutionModule()}]

[{assign var='installmentSliderRateTranslation' value="PAYO_PAYMENT_INSTALLMENT_RATE"|oxmultilangassign}]
[{oxscript add="var installmentSliderRateTranslation = '"|cat:$installmentSliderRateTranslation|cat:"';"}]
[{oxscript add="var activeLanguageAbbreviation = '"|cat:$oViewConf->getActLanguageAbbr()|cat:"';"}]
[{oxscript add="var activeBillingCountry = '"|cat:$oViewConf->getActiveCountryIso()|cat:"';"}]
[{oxscript include=$oxPayolutionScript priority=10}]
[{oxscript include=$oViewConf->getModuleUrl("payolution", "out/src/js/libs/payolution.jquery.min.js") priority=1}]
[{oxscript include=$oViewConf->getModuleUrl("payolution", "out/src/js/libs/payolution.jquery-ui.min.js") priority=2}]
[{oxscript include=$oViewConf->getPayolutionModuleUrl("out/src/js/libs/accounting.js") priority=10}]
[{oxscript include=$oViewConf->getPayolutionModuleUrl("out/src/js/libs/payolution/payo-installment.js") priority=10}]
[{oxscript include=$oViewConf->getPayolutionModuleUrl("out/src/js/libs/jquery-ui-slider-pips.min.js") priority=11}]
[{oxscript include=$oViewConf->getModuleUrl("payolution", "out/src/js/libs/cookie/jquery.cookie.js") priority=9}]
[{oxscript include=$oViewConf->getPayolutionModuleUrl("out/src/js/payoInstallmentSlider.js") priority=10}]
[{oxscript include=$oViewConf->getPayolutionModuleUrl("out/src/js/payoPaymentInstallment.js") priority=13}]
[{oxstyle  include=$oViewConf->getPayolutionModuleUrl("out/src/css/payolution.css") priority=10}]
[{if $oViewConf->isActiveThemeFlow()}]
    [{oxstyle  include=$oViewConf->getPayolutionModuleUrl("out/src/css/payolution-flow.css") priority=10}]
[{/if}]
[{if $oViewConf->isActiveThemeWave()}]
    [{oxstyle  include=$oViewConf->getPayolutionModuleUrl("out/src/css/payolution-wave.css") priority=10}]
[{/if}]
[{oxstyle  include=$oViewConf->getPayolutionModuleUrl("out/src/css/jquery-ui.min.css") priority=11}]
[{oxstyle  include=$oViewConf->getPayolutionModuleUrl("out/src/css/jquery-ui-slider-pips.css") priority=11}]
[{oxscript add="var oxGlobalPayolutionPnoSsnEncoding = '';"}]
[{oxscript add='payolution_jQuery(function($){$("form.js-oxValidate, form.oxValidate").oxPayolutionInputValidator();});'}]
[{if $oViewConf->isActiveThemeFlow() || $oViewConf->isActiveThemeWave()}]
    [{assign var="containerElement" value="div"}]
    [{assign var="paymentTag" value="div"}]
[{else}]
    [{assign var="containerElement" value="li"}]
    [{assign var="paymentTag" value="ul"}]
[{/if}]
[{assign var="sCurLangAbbr" value=""}]
[{foreach from=$oxcmp_lang item=_lng}]
    [{if $_lng->selected}]
        [{assign var="sCurLangAbbr" value=$_lng->abbr}]
    [{/if}]
[{/foreach}]
[{assign var="config" value=$payolution->getConfig()}]
[{assign var="base64cmp" value=$config->getBase64EncodedCompanyName()}]
<input type="hidden" id="base64cmp" value="[{$base64cmp}]"/>
[{if $sPaymentID == "payolution_invoice_b2c" || $sPaymentID == "payolution_invoice_b2b" || $sPaymentID == "payolution_installment" || $sPaymentID == "payolution_dd"}]
    <dl [{if $oViewConf->isActiveThemeFlow() || $oViewConf->isActiveThemeWave()}] class="payolution-select-payment-form"[{/if}]>
        <dt>
            <input id="payment_[{$sPaymentID}]" type="radio" name="paymentid" value="[{$sPaymentID}]" [{if $oView->getCheckedPaymentId() == $paymentmethod->oxpayments__oxid->value}]checked[{/if}]>
            <label for="payment_[{$sPaymentID}]"><b>[{ $paymentmethod->oxpayments__oxdesc->value}]</b></label>
        </dt>
        <dd class="payolution-flow-form-container [{if $oView->getCheckedPaymentId() == $paymentmethod->oxpayments__oxid->value}]activePayment[{/if}]">
            <[{$paymentTag}] [{if $sPaymentID != "payolution_installment"}] class="form payolutionUserDetails payolution_usertext_[{ $sPaymentID }]"[{/if}] style="display: inline-block">
            [{assign var="module" value=$oViewConf->getPayolutionModule()}]
            [{if $oViewConf->getPaymentPrice($paymentmethod)}]
                <div>
                    [{assign var="oPaymentPrice" value=$oViewConf->getPaymentPrice($paymentmethod) }]
                    [{if $oViewConf->isOxidFunctionalityEnabled('blShowVATForPayCharge') }]
                        ([{ $oViewConf->formatPrice($oPaymentPrice->getNettoPrice(), $currency) }] [{ oxmultilang ident="PLUS_VAT" }] [{ $oViewConf->formatPrice($oPaymentPrice->getVatValue(), $currency) }])
                    [{else}]
                        ([{ $oViewConf->formatPrice($oPaymentPrice->getBruttoPrice(), $currency) }])
                    [{/if}]
                </div>
            [{/if}]
            [{assign var=blVisibleText_fname value=false}]
            [{assign var="payolutionForms" value=$payolution->forms()}]

            [{assign var="session" value=$oViewConf->getSession()}]

            [{assign var="paymentContext" value=$payolution->createOrderingContext($oViewConf->getUser(), '', $session->getBasket(), null)}]
            [{assign var="form" value=$payolutionForms->getPaymentFormCached($sPaymentID, $paymentContext, $smarty.request)}]

            [{assign var="validation" value=$payolution->validation()}]

            [{if $validation->isShippingAddressAllowed($paymentContext)}]
                [{if $sPaymentID == "payolution_installment"}]
                    [{include file="inc/select_payment_installment.tpl" form=$form paymentContext=$paymentContext sPaymentID=$sPaymentID}]
                [{else}]
                    <span class="payoBold">[{oxmultilang ident="PAYO_PAYMENT_FILL_DATA"}]:</span>
                        [{if $sPaymentID == "payolution_invoice_b2b" && $oViewConf->isActiveThemeFlow() }]
                            [{include file="invoice_b2b_select_payment_flow.tpl"}]
                        [{else}]
                            <[{$containerElement}]>
                                [{foreach from=$form->getElements() item=element}]
                                    [{include file="inc/select_payment_element.tpl" element=$element}]
                                [{/foreach}]
                            </[{$containerElement}]>
                        [{/if}]
                [{/if}]
            [{else}]
                <input id="payoDisableNextStep" value="1" type="hidden"/>
                [{oxmultilang ident="EXCEPTION_PAYOLUTION_INPUT_DIFFERENT_ADDRESSES"}]
            [{/if}]

            [{block name="checkout_payment_longdesc"}]
                [{if $paymentmethod->oxpayments__oxlongdesc->value}]
                    <div class="desc">
                        [{ $paymentmethod->oxpayments__oxlongdesc->getRawValue()}]
                    </div>
                [{/if}]
            [{/block}]
            </[{$paymentTag}]>
        </dd>
    </dl>
[{else}]
    [{$smarty.block.parent}]
[{/if}]
