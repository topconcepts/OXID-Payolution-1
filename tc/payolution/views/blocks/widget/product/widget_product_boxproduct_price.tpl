[{$smarty.block.parent}]

    [{assign var="price" value=$_oBoxProduct->getPrice()}]
    [{assign var="module" value=$oViewConf->getPayolutionModule()}]
    [{assign var="validation" value=$module->validation()}]
    [{assign var="config" value=$module->getConfig()}]
    [{if $config->showInstallmentPriceOnHomePage() && $validation->installmentAvailable($price)}]
    [{oxscript include=$oViewConf->getModuleUrl("payolution", "out/src/js/libs/payolution.jquery.min.js") priority=1}]
        [{assign var='installmentSliderRateTranslation' value="PAYO_PAYMENT_INSTALLMENT_RATE"|oxmultilangassign}]
        [{oxscript add="var installmentSliderRateTranslation = '"|cat:$installmentSliderRateTranslation|cat:"';"}]
        [{assign var='payoInstallmentJsUrl' value=$oViewConf->getModuleUrl("payolution", "out/src/js/libs/payolution/payo-installment.js")|cat:'?'|cat:$oViewConf->getPayoJsUpdateTime()}]
        [{oxscript include=$payoInstallmentJsUrl priority=10}]
        [{oxscript include=$oViewConf->getModuleUrl("payolution", "out/src/js/libs/cookie/jquery.cookie.js") priority=9}]
        [{oxscript include=$oViewConf->getModuleUrl("payolution", "out/src/js/payoInstallmentSlider.js") priority=10}]
        [{oxscript include=$oViewConf->getModuleUrl("payolution", "out/src/js/widgets/payo-listitem-infogrid.js") priority=10}]
        <div class="payoOldPrice oldPrice" data-price="[{$price->getPriceInActCurrency($price->getPrice())}]" data-currency="[{$currency->sign}]">
            [{oxmultilang ident="PAYOLUTION_FROM"}] <span class="payoPrice"></span> / [{oxmultilang ident="PAYOLUTION_PAYMENT_PER_MONTH"}]
            <br/>(<a href="javascript:void(0)" class="payoReadMore readMore">[{oxmultilang ident="PAYOLUTION_READ_MORE"}]</a>)
        </div>
    [{/if}]
