[{$smarty.block.parent}]

[{if $payment->oxpayments__oxid->value == 'payolution_installment'}]
    [{assign var="price" value=$oxcmp_basket->getPrice()}]

    [{assign var="payolution" value=$oViewConf->getPayolutionModule()}]
    [{assign var="session" value=$oViewConf->getSession()}]
    [{assign var="dynvalue" value=$session->getVariable('dynvalue')}]
    [{assign var="pdfIcon" value=$oViewConf->getModuleUrl("payolution", "out/src/img/pdf.png")}]

    [{assign var='installmentSliderRateTranslation' value="PAYO_PAYMENT_INSTALLMENT_RATE"|oxmultilangassign}]
    [{oxscript add="var installmentSliderRateTranslation = '"|cat:$installmentSliderRateTranslation|cat:"';"}]
    [{oxstyle include=$oViewConf->getModuleUrl("payolution", "out/src/css/payolution.css") priority=10}]
    [{oxscript include=$oViewConf->getModuleUrl("payolution", "out/src/js/libs/payolution.jquery.min.js") priority=1}]
    [{oxscript include=$oViewConf->getModuleUrl("payolution", "out/src/js/libs/accounting.js") priority=10}]
    [{assign var='payoInstallmentJsUrl' value=$oViewConf->getModuleUrl("payolution", "out/src/js/libs/payolution/payo-installment.js")|cat:'?'|cat:$oViewConf->getPayoJsUpdateTime()}]
    [{oxscript include=$payoInstallmentJsUrl priority=10}]
    [{oxscript include=$oViewConf->getModuleUrl("payolution", "out/src/js/libs/jquery-ui-slider-pips.min.js") priority=10}]
    [{oxscript include=$oViewConf->getModuleUrl("payolution", "out/src/js/libs/cookie/jquery.cookie.js") priority=9}]
    [{oxscript include=$oViewConf->getModuleUrl("payolution", "out/src/js/payoInstallmentSlider.js") priority=10}]
    [{oxscript include=$oViewConf->getModuleUrl("payolution", "out/src/js/payo-order-step.js") priority=10}]
    <a href="javascript:void(0)" id="payo-toggler-show" class="readMore payo-order-installment-details-toggler">[{oxmultilang ident=PAYO_PAYMENT_INSTALLMENT_SHOW_DETAILS}]</a>
    <a href="javascript:void(0)" id="payo-toggler-hide" class="readMore payo-order-installment-details-toggler">[{oxmultilang ident=PAYO_PAYMENT_INSTALLMENT_HIDE_DETAILS}]</a>
    <div id="payo-order-installment-details">
        <input type="hidden" id="payo-order-sum" value="[{$price->getPrice()}]"/>
        <input type="hidden" id="payo-order-payolution_installment_period" value="[{$dynvalue.payolution_installment_period}]"/>
        <div id="installment-slider"></div>
        <div class="payo-installment-wrapper-left">
            <table id="payo-installment-data">
                <tr>
                    <th>[{oxmultilang ident=PAYO_PAYMENT_INSTALLMENT_SUM}]:</th>
                    <td id="payo-installment-sum"></td>
                </tr>
                <tr>
                    <th>[{oxmultilang ident=PAYO_PAYMENT_INSTALLMENT_PERIOD}]:</th>
                    <td id="payo-installment-period"></td>
                </tr>
                <tr>
                    <th>[{oxmultilang ident=PAYO_PAYMENT_INSTALLMENT_MONTHLY_SUM}]:</th>
                    <td id="payo-installment-monthly-sum"></td>
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
                    <th>[{oxmultilang ident=PAYO_PAYMENT_INSTALLMENT_TOTAL_SUM}]:</th>
                    <td id="payo-installment-total-sum"></td>
                </tr>
            </table>
        </div>
        <div class="payo-installment-wrapper-right">
            <div id="payo-installment-details"></div>
            <a id="payo-pdf" href="javascript:void()" data-pdf-base-url='[{oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=PayolutionPdfDownload"}]' class="readMore" >[{oxmultilang ident="PAYOLUTION_INSTALLMENT_DRAFT_CREDIT_AGREEMENT"}]</a>
            <img src="[{$pdfIcon}]" alt="PDF" width="12" height="12"/>
        </div>
        <div style="clear:both"></div>
    </div>
[{/if}]
