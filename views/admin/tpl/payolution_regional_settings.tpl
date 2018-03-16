[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]

[{assign var="sCssPath" value=$oViewConf->getPayolutionModuleUrl('out/admin/src/css/payolution_admin.css')}]
[{assign var="sjQuery" value=$oViewConf->getPayolutionModuleUrl('out/src/js/libs/jquery.min.js')}]
<link rel="stylesheet" href="[{$sCssPath}]">
<script src="[{$sjQuery}]"></script>
[{if $readonly }]
    [{assign var="readonly" value="readonly disabled"}]
[{else}]
    [{assign var="readonly" value=""}]
[{/if}]

<script type="text/javascript">
    jQuery.noConflict();
    jQuery( document ).ready(function() {
        jQuery('.payolution-country-row .row').click(function() {
            jQuery(this).find('.sign').toggleClass('minus');
            jQuery(this).parent().find('.country-options').toggle();
        });
    });
</script>

<div class="main-container">
    <div class="payolution-intro">
        <div class="payolution-intro-text">
            <h1 class="payolution">
                [{oxmultilang ident="poregionalsettings"}]
            </h1>
            <p class="mg0">
               [{oxmultilang ident="REGIONAL_SETTINGS_DESCRIPTION"}]
            </p>
        </div>
        <div class="payolution-logo"></div>
        <div class="clear"></div>
    </div>
    <div class="payolution-expandable-list">
        <form name="myedit" id="myedit" method="post" action="[{$oViewConf->getSelfLink()}]" enctype="multipart/form-data">
            [{$oViewConf->getHiddenSid() }]
            <input type="hidden" name="cl" value="payolution_regional">
            <input type="hidden" name="fnc" value="">
            [{foreach from=$oViewConf->getPayolutionCountries() key=iKey item=oCountry}]
                [{assign var=sCountryTag value=$oCountry->oxcountry__oxisoalpha2->value}]
                [{assign var=sArrNameTmp value=blPayolutionActiveCountry`$sCountryTag`}]
                <div class="payolution-country-row">
                    <div class="row">
                        <div class="sign plus"></div>
                        <div class="text [{if !$confbools.$sArrNameTmp}]inactive[{/if}]">
                            [{$oCountry->oxcountry__oxtitle->value}]
                        </div>
                        <div class="payolution-row-flag [{$sCountryTag|lower}]"></div>
                    </div>
                    <div class="clear"></div>
                    <table class="country-options" style="display: none;">
                        <tr class="dark">
                            <td class="name">
                                [{oxmultilang ident="PAYOLUTION_CONFIG_ACTIVE_COUNTRY"}]:
                            </td>
                            <td class="input w460">
                                [{assign var=sArrNameTmp value=blPayolutionActiveCountry`$sCountryTag`}]
                                <select class="normalSelect w267" name="confbools[blPayolutionActiveCountry[{$sCountryTag}]]" >
                                    <option value="0">[{oxmultilang ident="PAYOLUTION_INNACTIVE"}]</option>
                                    <option value="1" [{if ($confbools.$sArrNameTmp)}]selected="selected"[{/if}]>[{oxmultilang ident="PAYOLUTION_ACTIVE"}]</option>
                                </select>
                            </td>
                            <td class="infoblock">
                                [{oxinputhelp ident="HELP_PAYOLUTION_CONFIG_ACTIVE_COUNTRY"}]
                            </td>
                        </tr>
                        <tr>
                            <td class="name">
                                [{oxmultilang ident="PAYOLUTION_ACTIVE_CHECKOUT"}]:
                            </td>
                            <td class="input">
                                [{assign var=sArrNameTmp value=blPayolutionActiveCheckout`$sCountryTag`}]
                                <select class="normalSelect w267" name="confbools[blPayolutionActiveCheckout[{$sCountryTag}]]" >
                                    <option value="0">[{oxmultilang ident="PAYOLUTION_INNACTIVE"}]</option>
                                    <option value="1" [{if ($confbools.$sArrNameTmp)}]selected="selected"[{/if}]>[{oxmultilang ident="PAYOLUTION_ACTIVE"}]</option>
                                </select>
                            </td>
                            <td class="infoblock">
                                [{oxinputhelp ident="HELP_PAYOLUTION_ACTIVE_CHECKOUT"}]
                            </td>
                        </tr>
                        <tr class="dark">
                            <td class="name">
                                [{oxmultilang ident="PAYOLUTION_CONFIG_MERCHANTID"}]:
                            </td>
                            <td class="input">
                                [{assign var=sArrNameTmp value=sPayolutionMerchantId`$sCountryTag`}]
                                <input type=text class="normal payolution-inside-shadow w250" name=confstrs[sPayolutionMerchantId[{$sCountryTag}]] value="[{$confstrs.$sArrNameTmp}]">
                            </td>
                            <td class="infoblock">[{oxinputhelp ident="HELP_PAYOLUTION_CONFIG_MERCHANTID"}]</td>
                        </tr>
                        <tr>
                            <td class="name">
                                [{oxmultilang ident="PAYOLUTION_CONFIG_SHARED_SECRET" }]:
                            </td>
                            <td class="input">
                                [{assign var=sArrNameTmp value=sPayolutionSharedSecret`$sCountryTag`}]
                                <input type=text class="normal payolution-inside-shadow w250" name=confstrs[sPayolutionSharedSecret[{$sCountryTag}]] value="[{$confstrs.$sArrNameTmp}]">
                            </td>
                            <td class="infoblock">
                                [{oxinputhelp ident="HELP_PAYOLUTION_CONFIG_SHARED_SECRET"}]
                            </td>
                        </tr>
                        <tr class="dark">
                            <td class="name">
                                [{oxmultilang ident="PAYOLUTION_CONFIG_INVOICE_FEE"}]:
                            </td>
                            <td class="input">
                                [{assign var=sArrNameTmp value=iPayolutionInvoiceFee`$sCountryTag`}]
                                <input type="text" poaceholder="[{oxmultilang ident="PAYOLUTION_ENTER_AMOUNT"}]" class="normal payolution-inside-shadow" name=confstrs[iPayolutionInvoiceFee[{$sCountryTag}]] value="[{$confstrs.$sArrNameTmp}]">&nbsp;[{$oView->getCurrencyAbbr($sCountryTag)}]
                            </td>
                            <td class="infoblock">
                                [{oxinputhelp ident="HELP_PAYOLUTION_CONFIG_INVOICE_FEE"}]
                            </td>
                        </tr>
                        <tr>
                            <td class="name">
                                [{oxmultilang ident="PAYOLUTION_CONFIG_MNTHLY_RT_MIN_AMOUNT"}]:
                            </td>
                            <td class="input">
                                [{assign var=sArrNameTmp value=iPayolutionMonthlyRateMinAmount`$sCountryTag`}]
                                <input poaceholder="[{oxmultilang ident="PAYOLUTION_ENTER_AMOUNT"}]" type=text class="normal payolution-inside-shadow" name=confstrs[iPayolutionMonthlyRateMinAmount[{$sCountryTag}]] value="[{$confstrs.$sArrNameTmp}]">&nbsp;[{$oView->getCurrencyAbbr($sCountryTag)}]
                            </td>
                            <td class="infoblock">
                                [{oxinputhelp ident="HELP_PAYOLUTION_CONFIG_MNTHLY_RT_MIN_AMOUNT"}]
                            </td>
                        </tr>
                        <tr class="dark">
                            <td class="name">
                                [{oxmultilang ident="PAYOLUTION_COUNTRY_TERMS_URL"}]:
                            </td>
                            <td class="input">
                                [{assign var=sArrNameTmp value=sPayolutionTermsUrl`$sCountryTag`}]
                                <input poaceholder="[{oxmultilang ident="PAYOLUTION_ENTER_URL"}]" type=text class="normal payolution-inside-shadow w420" name=confstrs[sPayolutionTermsUrl[{$sCountryTag}]] value="[{$confstrs.$sArrNameTmp}]">
                            </td>
                            <td class="infoblock">
                                [{oxinputhelp ident="HELP_PAYOLUTION_COUNTRY_TERMS_URL"}]
                            </td>
                        </tr>
                        <tr>
                            <td class="name">
                                [{oxmultilang ident="PAYOLUTION_SEPARATE_SHIPPING"}]:
                            </td>
                            <td class="input">
                                [{assign var=sArrNameTmp value=blPayolutionSeparateShipping`$sCountryTag`}]
                                <input type="hidden" name="confbools[blPayolutionSeparateShipping[{$sCountryTag}]]" value="0">
                                <input type="checkbox" name="confbools[blPayolutionSeparateShipping[{$sCountryTag}]]" value="1"
                                [{if ($confbools.$sArrNameTmp)}]checked[{/if}] [{ $readonly}]>
                            </td>
                            <td class="infoblock">
                                [{oxinputhelp ident="HELP_PAYOLUTION_SEPARATE_SHIPPING"}]
                            </td>
                        </tr>
                        <tr class="dark">
                            <td class="name">
                                [{oxmultilang ident="PAYOLUTION_MANDATORY_PHONE"}]:
                            </td>
                            <td class="input">
                                [{assign var=sArrNameTmp value=blPayolutionMandatoryPhone`$sCountryTag`}]
                                <input type="hidden" name="confbools[blPayolutionMandatoryPhone[{$sCountryTag}]]" value="0">
                                <input type="checkbox" name="confbools[blPayolutionMandatoryPhone[{$sCountryTag}]]" value="1"
                                [{if ($confbools.$sArrNameTmp)}]checked[{/if}] [{ $readonly}]>
                            </td>
                            <td class="infoblock">
                                [{oxinputhelp ident="HELP_PAYOLUTION_MANDATORY_PHONE"}]
                            </td>
                        </tr>
                    </table>
                    <div class="clear"></div>
                </div>
            [{/foreach}]
            <input type="submit" name="save" class="payolution-blue-button corners mgt20" value="[{oxmultilang ident="GENERAL_SAVE"}]" onClick="Javascript:document.myedit.fnc.value='save'" [{$readonly}]>
        </form>
    </div>
</div>
