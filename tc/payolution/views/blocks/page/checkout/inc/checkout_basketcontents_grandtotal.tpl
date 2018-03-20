[{$smarty.block.parent}]
[{if $oViewConf->getActiveClassName() == 'basket'}]
    [{assign var="price" value=$oxcmp_basket->getPrice()}]
    [{assign var="module" value=$oViewConf->getPayolutionModule()}]
    [{assign var="validation" value=$module->validation()}]
    [{assign var="config" value=$module->getConfig()}]
    [{if $config->showInstallmentPriceOnBasket() && $validation->installmentAvailable($price)}]
        <tr>
            <td colspan="2">
                [{assign var='installmentSliderRateTranslation' value="PAYO_PAYMENT_INSTALLMENT_RATE"|oxmultilangassign}]
                [{oxscript add="var installmentSliderRateTranslation = '"|cat:$installmentSliderRateTranslation|cat:"';"}]
                [{oxscript include=$oViewConf->getModuleUrl("payolution", "out/src/js/libs/payolution.jquery.min.js") priority=1}]
                [{oxscript include=$oViewConf->getModuleUrl("payolution", "out/src/js/libs/accounting.js") priority=10}]
                [{assign var='payoInstallmentJsUrl' value=$oViewConf->getModuleUrl("payolution", "out/src/js/libs/payolution/payo-installment.js")|cat:'?'|cat:$oViewConf->getPayoJsUpdateTime()}]
                [{oxscript include=$payoInstallmentJsUrl priority=10}]
                [{oxscript include=$oViewConf->getModuleUrl("payolution", "out/src/js/libs/cookie/jquery.cookie.js") priority=9}]
                [{oxscript include=$oViewConf->getModuleUrl("payolution", "out/src/js/payoInstallmentSlider.js") priority=10}]
                [{oxscript include=$oViewConf->getModuleUrl("payolution", "out/src/js/widgets/payo-listitem-infogrid.js") priority=10}]
                <div style="clear:both"></div>
                <div class="payoBold payoLeft payoSmall">
                    <span class="payoOldPrice oldPrice payoBold" data-price="[{$price->getPriceInActCurrency($price->getPrice())}]" data-currency="[{$currency->sign}]"  style="display: none;">
                        <span class="oldPrice payoBold">[{oxmultilang ident="PAYO_ORDER_INSTALLMENT_MONTHLY_SUM"}] [{oxmultilang ident="PAYOLUTION_FROM"}]</span>
                        <span class="payoPrice payoBold"></span> / [{oxmultilang ident="PAYOLUTION_PAYMENT_PER_MONTH"}]
                        <br/>
                        <a href="javascript:void(0)" class="payoReadMore readMore">[{oxmultilang ident="PAYOLUTION_READ_MORE"}]</a>
                    </span>
                </div>
            </td>
        </tr>
    [{/if}]
[{/if}]
