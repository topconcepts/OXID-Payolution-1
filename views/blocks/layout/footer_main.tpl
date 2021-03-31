[{$smarty.block.parent}]
[{assign var="module" value=$oViewConf->getPayolutionModule()}]
[{assign var="sessionCurrency" value=$oViewConf->getSessionCurrency()|@json_encode}]
<script type="text/javascript">
    var sessionCurrency=[{$sessionCurrency}];
</script>

[{if $module->shouldIncludeModal()}]
    [{assign var='installmentSliderRateTranslation' value="PAYO_PAYMENT_INSTALLMENT_RATE"|oxmultilangassign}]
    [{oxscript add="var installmentSliderRateTranslation = '"|cat:$installmentSliderRateTranslation|cat:"';"}]
    [{oxstyle include=$oViewConf->getModuleUrl("payolution", "out/src/css/jquery-ui.min.css") priority=1}]
    [{oxstyle include=$oViewConf->getModuleUrl("payolution", "out/src/css/jquery-ui-slider-pips.css") priority=10}]
    [{oxstyle include=$oViewConf->getModuleUrl("payolution", "out/src/css/payolution.css") priority=11}]
    [{if $oViewConf->isActiveThemeFlow()}]
        [{oxstyle  include=$oViewConf->getPayolutionModuleUrl("out/src/css/payolution-flow.css") priority=12}]
    [{/if}]
    [{if $oViewConf->isActiveThemeWave()}]
        [{oxstyle  include=$oViewConf->getPayolutionModuleUrl("out/src/css/payolution-wave.css") priority=12}]
    [{/if}]
    [{oxscript include=$oViewConf->getModuleUrl("payolution", "out/src/js/libs/payolution.jquery.min.js") priority=1}]
    [{oxscript include=$oViewConf->getModuleUrl("payolution", "out/src/js/libs/payolution.jquery-ui.min.js") priority=2}]
    [{oxscript include=$oViewConf->getModuleUrl("payolution", "out/src/js/widgets/oxmodalpopup.js") priority=10}]
    [{oxscript include=$oViewConf->getModuleUrl("payolution", "out/src/js/libs/accounting.js") priority=10}]
    [{oxscript include=$oViewConf->getModuleUrl("payolution", "out/src/js/libs/jquery-ui-slider-pips.min.js") priority=9}]
    [{assign var='payoInstallmentJsUrl' value=$oViewConf->getModuleUrl("payolution", "out/src/js/libs/payolution/payo-installment.js")|cat:'?'|cat:$oViewConf->getPayoJsUpdateTime()}]
    [{oxscript include=$payoInstallmentJsUrl priority=10}]
    [{oxscript include=$oViewConf->getModuleUrl("payolution", "out/src/js/libs/cookie/jquery.cookie.js") priority=9}]
    [{oxscript include=$oViewConf->getModuleUrl("payolution", "out/src/js/payoInstallmentSlider.js") priority=10}]
    [{oxscript include=$oViewConf->getModuleUrl("payolution", "out/src/js/payo-installment-modal.js") priority=10}]
    <input type="hidden" id="payo-translation-due" value="[{oxmultilang ident='PAYOLUTION_DUE'}]"/>
    <div id="payoInstallmentModal" class="payoSlider">
        <img style="position: absolute; right: 0.5em; top: 0.5em; cursor: pointer;" class="closePop" alt="" src="[{$oViewConf->getImageUrl('x.png')}]">
        <input type="hidden" id="payo-order-sum"/>

        <h2>[{oxmultilang ident="PAYO_INSTALLMENT_MODAL_TITLE"}]</h2>
        <div>[{oxmultilang ident="PAYO_INSTALLMENT_MODAL_TEXT"}]</div>

        <div id="installment-slider"></div>
        <div class="payo-installment-wrapper-left">
            <table id="payo-installment-data">
                <tr>
                    <th>[{oxmultilang ident=PAYO_PAYMENT_INSTALLMENT_SUM}]:</th>
                    <td id="payo-installment-sum"></td>
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
        <div style="clear:both"></div>
        <a href="javascript:void(0)" id="payo-installment-details-toggler" class="readMore" data-show="[{oxmultilang ident=PAYO_PAYMENT_INSTALLMENT_SHOW_DETAILS}]" data-hide="[{oxmultilang ident=PAYO_PAYMENT_INSTALLMENT_HIDE_DETAILS}]">[{oxmultilang ident=PAYO_PAYMENT_INSTALLMENT_SHOW_DETAILS}]</a>
        <div id="payo-installment-details"></div>
        <div class="description">[{oxmultilang ident="PAYO_INSTALLMENT_MODAL_DESCRIPTION"}]</div>
    </div>
[{/if}]
