[{$smarty.block.parent}]
[{if !$oDetailsProduct->isNotBuyable()}]
    [{capture assign=calculateInstalmentScript}]
        [{fetch file=$oViewConf->getModulePath('payolution', 'out/src/js/widgets/payo-listitem-infogrid.js')}]
    [{/capture}]

    [{assign var="price" value=$oDetailsProduct->getPrice()}]
    [{if $oDetailsProduct->isParentNotBuyable() }]
        [{assign var="price" value=$oDetailsProduct->getVarMinPrice()}]
    [{/if}]
    [{assign var="module" value=$oViewConf->getPayolutionModule()}]
    [{assign var="validation" value=$module->validation()}]
    [{assign var="config" value=$module->getConfig()}]
    [{if $config->showInstallmentPriceOnDetailsPage() && $validation->installmentAvailable($price)}]
        [{assign var='installmentSliderRateTranslation' value="PAYO_PAYMENT_INSTALLMENT_RATE"|oxmultilangassign}]
        [{oxscript add="var installmentSliderRateTranslation = '"|cat:$installmentSliderRateTranslation|cat:"';"}]
        [{oxscript include=$oViewConf->getModuleUrl("tcpayolution", "out/src/js/libs/payolution.jquery.min.js") priority=1}]
        [{oxscript include=$oViewConf->getModuleUrl("tcpayolution", "out/src/js/libs/accounting.js") priority=10}]
        [{assign var='payoInstallmentJsUrl' value=$oViewConf->getModuleUrl("tcpayolution", "out/src/js/libs/payolution/payo-installment.js")|cat:'?'|cat:$oViewConf->getPayoJsUpdateTime()}]
        [{oxscript include=$payoInstallmentJsUrl priority=10}]
        [{oxscript include=$oViewConf->getModuleUrl("tcpayolution", "out/src/js/libs/cookie/jquery.cookie.js") priority=9}]
        [{oxscript include=$oViewConf->getModuleUrl("tcpayolution", "out/src/js/payoInstallmentSlider.js") priority=10}]
        [{oxscript add=$calculateInstalmentScript}]
        <div style="clear:both"></div>
        <span class="payoOldPrice oldPrice" data-price="[{$price->getPriceInActCurrency($price->getPrice())}]" data-currency="[{$currency->sign}]" style="display: none;">
            <span class="oldPrice">[{oxmultilang ident="PAYO_PAYMENT_INSTALLMENT_MONTHLY_SUM"}]</span>
            [{if $oDetailsProduct->isRangePrice()}]
                [{oxmultilang ident="PAYOLUTION_FROM"}]
            [{/if}]
            <span class="payoPrice payoBold"></span> / [{oxmultilang ident="PAYOLUTION_PAYMENT_PER_MONTH"}]
            <br/>
        <a href="javascript:void(0)" class="payoReadMore readMore">[{oxmultilang ident="PAYOLUTION_READ_MORE"}]</a>
        </span>
        <div style="clear:both"></div>
    [{/if}]
[{/if}]
