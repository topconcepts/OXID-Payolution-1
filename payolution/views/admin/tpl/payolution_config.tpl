[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]
[{assign var="sCssPath" value=$oViewConf->getPayolutionModuleUrl('out/admin/src/css/payolution_admin.css')}]
[{assign var="sjQuery" value=$oViewConf->getPayolutionModuleUrl('out/src/js/libs/jquery.min.js')}]
<link rel="stylesheet" href="[{$sCssPath}]">
<script src="[{$sjQuery}]"></script>
<script>
    function changeConfigLanguage(obj) {
        document.myedit.submit();
    }
</script>

[{if $readonly }]
    [{assign var="readonly" value="readonly disabled"}]
[{else}]
    [{assign var="readonly" value=""}]
[{/if}]

<script type="text/javascript">
    jQuery.noConflict();
    jQuery(document).ready(function () {

        var environmentOption = jQuery('#payolution-env-option');

        var liveConfigs = jQuery('.live-config');
        var testConfigs = jQuery('.test-config');

        function update() {
            var option = environmentOption.val();
            var liveServer = (option == 1);

            if (liveServer) {
                testConfigs.hide();
                liveConfigs.show();
            } else {
                liveConfigs.hide();
                testConfigs.show();
            }
        }

        environmentOption.change(update);
        update();

        function toggleConfigGroup(elm) {
            var row = jQuery(elm),
                btn = row.find('.sign');

            btn.toggleClass('minus');
            row.toggleClass('row-open');
            row.parent().find('.country-options').toggle();
        }

        jQuery('.payolution-country-row .row').click(function () {
            toggleConfigGroup(this);
        });

        jQuery("#preorder").keypress(function (e) {
            if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
                return false;
            }
        });

        jQuery('#preorder').blur(function() {
            if (jQuery(this).val() == '') {
                jQuery(this).val(7);
            }
        });

        jQuery('#countrySelector').change(function (e) {
            e.preventDefault();
            var selectedCountry = jQuery(this).val() || 'default';
            if (selectedCountry) {
                jQuery('.country').hide();
                jQuery('.country.'+selectedCountry).show();
            }
        });
    });
</script>

<div class="main-container">
<div class="payolution-intro">
    <div class="payolution-intro-text">
        <h1 class="payolution">
            [{oxmultilang ident="payolutionconfig"}]
        </h1>

        <p class="mg0">
            [{assign var="sConfiguration" value="CONFIGURATION_DESCRIPTION"|oxmultilangassign}]
            [{assign var="sSelfLink" value=$oViewConf->getSelfLink()}]
            [{assign var="sLink" value="`$sSelfLink`&cl=payolution_install"}]
            [{$sConfiguration|replace:'{link}':$sLink}]
        </p>
    </div>
    <div class="payolution-logo"></div>
    <div class="clear"></div>
</div>

<div class="payolution-expandable-list">
<form name="myedit" id="myedit" method="post" action="[{$oViewConf->getSelfLink()}]" enctype="multipart/form-data">
<input type="hidden" name="MAX_FILE_SIZE" value="[{$iMaxUploadFileSize}]">
[{$oViewConf->getHiddenSid()}]
<input type="hidden" name="cl" value="payolution_config">
<input type="hidden" name="fnc" value="">

[{*operation mode*}]
<table class="country-options">
    <tr>
        <td class="w340">
            <div class="payo-server-config-title-block">
                <div class="payo-logo-small"></div>
                <div class="payo-config-title">[{oxmultilang ident="PAYOLUTION_CONFIG_EXTENSION_MODE"}]:</div>
            </div>
        </td>
        <td class="input w290">
            <select id="payolution-env-option" name="confstrs[iPayolutionServerMode]" class="normalSelect w267" [{$readonly}]>
                <option value="0"
                        [{if $confstrs.iPayolutionServerMode == "0"}]selected="selected"[{/if}]>
                    [{oxmultilang ident="PAYOLUTION_CONFIG_TEST"}]
                </option>
                <option value="1"
                        [{if $confstrs.iPayolutionServerMode == "1"}]selected="selected"[{/if}]>
                    [{oxmultilang ident="PAYOLUTION_CONFIG_LIVE"}]
                </option>
            </select>
        </td>
        <td class="infoblock">
            [{oxinputhelp ident="HELP_PAYOLUTION_CONFIG_EXTENSION_MODE"}]
        </td>
    </tr>
</table>


[{*login data*}]
<div class="payolution-country-row">
    <div class="row">
        <div class="sign plus"></div>
        <div class="text">
            [{oxmultilang ident="PAYOLUTION_LOGIN_SETTINGS"}]
        </div>
    </div>
    <div class="clear"></div>
    <table class="country-options" style="display: none;">

        <tr>
            <td class="w340">
                [{oxmultilang ident="PAYOLUTION_CONFIG_LOGIN"}]
            </td>
            <td class="w290">
                <input autocomplete="off" type="text" class="live-config normal payolution-inside-shadow w250" name="confstrs[sPayolutionLogin]" value="[{$confstrs.sPayolutionLogin}]">
                <input autocomplete="off" type="text" class="test-config normal payolution-inside-shadow w250" name="confstrs[sPayolutionLoginTest]" value="[{$confstrs.sPayolutionLoginTest}]">
            </td>
            <td class="infoblock">
                [{oxinputhelp ident="HELP_PAYOLUTION_CONFIG_LOGIN" }]
            </td>
        </tr>

        <tr>
            <td class="w340">
                [{oxmultilang ident="PAYOLUTION_CONFIG_PASSWORD"}]
            </td>
            <td class="w290">
                <input autocomplete="off" type="text" class="live-config normal payolution-inside-shadow w250" name="confstrs[sPayolutionPassword]" value="[{$confstrs.sPayolutionPassword}]">
                <input autocomplete="off" type="text" class="test-config normal payolution-inside-shadow w250" name="confstrs[sPayolutionPasswordTest]" value="[{$confstrs.sPayolutionPasswordTest}]">
            </td>
            <td class="infoblock">
                [{oxinputhelp ident="HELP_PAYOLUTION_CONFIG_PASSWORD" }]
            </td>
        </tr>
    </table>
</div>


[{*channel data*}]
<div class="payolution-country-row">
    <div class="row">
        <div class="sign plus"></div>
        <div class="text">
            [{oxmultilang ident="PAYOLUTION_CHANNEL_SETTINGS"}]
        </div>
    </div>
    <div class="clear"></div>
    <table class="country-options" style="display: none;">
        <tr>
            <td colspan="3">
                <div class="text">[{oxmultilang ident="PAYOLUTION_CONFIG_ADDITIONAL_SYNC_SETUP"}]</div>
            </td>
        </tr>
        <tr>
            <td class="w340">
                [{oxmultilang ident="PAYOLUTION_CONFIG_CHANNEL_CL"}]
            </td>
            <td class="w290">
                <input type="text" class="live-config normal payolution-inside-shadow w250" name="confstrs[sPayolutionChannelCL]" value="[{$confstrs.sPayolutionChannelCL}]">
                <input type="text" class="test-config normal payolution-inside-shadow w250" name="confstrs[sPayolutionChannelCLTest]" value="[{$confstrs.sPayolutionChannelCLTest}]">
            </td>
            <td class="infoblock">
                [{oxinputhelp ident="HELP_PAYOLUTION_CONFIG_CHANNEL_CL" }]
            </td>
        </tr>

        <tr>
            <td class="w340">
                [{oxmultilang ident="PAYOLUTION_CONFIG_PASSWORD_CL"}]
            </td>
            <td class="w290">
                <input autocomplete="off" type="text" class="live-config normal payolution-inside-shadow w250" name="confstrs[sPayolutionPasswordCL]" value="[{$confstrs.sPayolutionPasswordCL}]">
                <input autocomplete="off" type="text" class="test-config normal payolution-inside-shadow w250" name="confstrs[sPayolutionPasswordCLTest]" value="[{$confstrs.sPayolutionPasswordCLTest}]">
            </td>
            <td class="infoblock">
                [{oxinputhelp ident="HELP_PAYOLUTION_CONFIG_PASSWORD_CL" }]
            </td>
        </tr>
    </table>
</div>


[{*optical settings*}]
<div class="payolution-country-row">
    <div class="row">
        <div class="sign plus"></div>
        <div class="text">
            [{oxmultilang ident="PAYOLUTION_OPTICAL_SETTINGS"}]
        </div>
    </div>
    <div class="clear"></div>
    <table class="country-options" style="display: none;">
        <tr>
            <td class="w340">
                [{oxmultilang ident="PAYOLUTION_CONFIG_INSTALLMENT_SHOW_PRICE_ON_DETAILS_PAGE"}]
            </td>
            <td class="w290">
                <select name="confstrs[bPayolutionShowPriceOnDetails]" class="normalSelect w272">
                    <option value="0"
                            [{if $confstrs.bPayolutionShowPriceOnDetails == "0"}]selected="selected"[{/if}]>
                        [{oxmultilang ident="PAYOLUTION_NO"}]
                    </option>
                    <option value="1"
                            [{if $confstrs.bPayolutionShowPriceOnDetails == "1"}]selected="selected"[{/if}]>
                        [{oxmultilang ident="PAYOLUTION_YES"}]
                    </option>
                </select>
            </td>
            <td class="infoblock">
                [{oxinputhelp ident="HELP_PAYOLUTION_CONFIG_INSTALLMENT_SHOW_PRICE_ON_DETAILS_PAGE" }]
            </td>
        </tr>
        <tr>
            <td class="w340">
                [{oxmultilang ident="PAYOLUTION_CONFIG_INSTALLMENT_SHOW_PRICE_ON_CATEGORY_PAGE"}]
            </td>
            <td class="w290">
                <select name="confstrs[bPayolutionShowPriceOnCategory]" class="normalSelect w272">
                    <option value="0"
                            [{if $confstrs.bPayolutionShowPriceOnCategory == "0"}]selected="selected"[{/if}]>
                        [{oxmultilang ident="PAYOLUTION_NO"}]
                    </option>
                    <option value="1"
                            [{if $confstrs.bPayolutionShowPriceOnCategory == "1"}]selected="selected"[{/if}]>
                        [{oxmultilang ident="PAYOLUTION_YES"}]
                    </option>
                </select>
            </td>
            <td class="infoblock">
                [{oxinputhelp ident="HELP_PAYOLUTION_CONFIG_INSTALLMENT_SHOW_PRICE_ON_CATEGORY_PAGE" }]
            </td>
        </tr>
        <tr>
            <td class="w340">
                [{oxmultilang ident="PAYOLUTION_CONFIG_INSTALLMENT_SHOW_PRICE_ON_HOMEPAGE"}]
            </td>
            <td class="w290">
                <select name="confstrs[bPayolutionShowPriceOnHomePage]" class="normalSelect w272">
                    <option value="0"
                            [{if $confstrs.bPayolutionShowPriceOnHomePage == "0"}]selected="selected"[{/if}]>
                        [{oxmultilang ident="PAYOLUTION_NO"}]
                    </option>
                    <option value="1"
                            [{if $confstrs.bPayolutionShowPriceOnHomePage == "1"}]selected="selected"[{/if}]>
                        [{oxmultilang ident="PAYOLUTION_YES"}]
                    </option>
                </select>
            </td>
            <td class="infoblock">
                [{oxinputhelp ident="HELP_PAYOLUTION_CONFIG_INSTALLMENT_SHOW_PRICE_ON_HOMEPAGE" }]
            </td>
        </tr>
        <tr>
            <td class="w340">
                [{oxmultilang ident="PAYOLUTION_CONFIG_INSTALLMENT_SHOW_PRICE_ON_BASKET"}]
            </td>
            <td class="w290">
                <select name="confstrs[bPayolutionShowPriceOnBasket]" class="normalSelect w272">
                    <option value="0"
                            [{if $confstrs.bPayolutionShowPriceOnBasket == "0"}]selected="selected"[{/if}]>
                        [{oxmultilang ident="PAYOLUTION_NO"}]
                    </option>
                    <option value="1"
                            [{if $confstrs.bPayolutionShowPriceOnBasket == "1"}]selected="selected"[{/if}]>
                        [{oxmultilang ident="PAYOLUTION_YES"}]
                    </option>
                </select>
            </td>
            <td class="infoblock">
                [{oxinputhelp ident="HELP_PAYOLUTION_CONFIG_INSTALLMENT_SHOW_PRICE_ON_BASKET" }]
            </td>
        </tr>
    </table>
</div>


[{*other settings*}]
<div class="payolution-country-row">
    <div class="row">
        <div class="sign plus"></div>
        <div class="text">
            [{oxmultilang ident="PAYOLUTION_OTHER_SETTINGS"}]
        </div>
    </div>
    <div class="clear"></div>
    <table class="country-options" style="display: none;">
        <tr>
            <td class="w340">
                [{oxmultilang ident="PAYOLUTION_CONFIG_DIFF_SHIP_ADDR" }]
            </td>
            <td class="w290">
                <select name="confstrs[blPayolutionAllowOtherShipAddr]" class="normalSelect w272" disabled="disabled">
                    <option value="0"
                            [{if $confstrs.blPayolutionAllowOtherShipAddr == "0"}]selected="selected"[{/if}]>
                        [{oxmultilang ident="PAYOLUTION_NO"}]
                    </option>
                    <option value="1"
                            [{if $confstrs.blPayolutionAllowOtherShipAddr == "1"}]selected="selected"[{/if}]>
                        [{oxmultilang ident="PAYOLUTION_YES"}]
                    </option>
                </select>
            </td>
            <td class="infoblock">
                [{oxinputhelp ident="HELP_PAYOLUTION_CONFIG_DIFF_SHIP_ADDR" }]
            </td>
        </tr>
        <tr>
            <td class="w340">
                [{oxmultilang ident="PAYOLUTION_CONFIG_ENABLE_LOGGING" }]
            </td>
            <td class="w290">
                <select name="confstrs[blPayolutionEnableLogging]" class="normalSelect w272">
                    <option value="0"
                            [{if $confstrs.blPayolutionEnableLogging == "0"}]selected="selected"[{/if}]>
                        [{oxmultilang ident="PAYOLUTION_OFF"}]
                    </option>
                    <option value="1"
                            [{if $confstrs.blPayolutionEnableLogging == "1"}]selected="selected"[{/if}]>
                        [{oxmultilang ident="PAYOLUTION_ON"}]
                    </option>
                </select>
            </td>
            <td class="infoblock">
                [{oxinputhelp ident="HELP_PAYOLUTION_CONFIG_ENABLE_LOGGING" }]
            </td>
        </tr>

        <tr>
            <td class="w340">
                [{oxmultilang ident="PAYOLUTION_CONFIG_EMAIL" }]
            </td>
            <td class="w290">
                <input type="text" class="normal payolution-inside-shadow w250" name="confstrs[sPayolutionInvoicePdfEmailAddess]" value="[{$confstrs.sPayolutionInvoicePdfEmailAddess}]">
            </td>
            <td class="infoblock">
                [{oxinputhelp ident="HELP_PAYOLUTION_CONFIG_EMAIL" }]
                <div class="preoder-error-message"></div>
            </td>
        </tr>

        <tr>
            <td class="w340">
                [{oxmultilang ident="PAYOLUTION_CONFIG_INSTALLMENT_MIN"}]
            </td>
            <td class="w290">
                <input type="text" class="normal payolution-inside-shadow w250" name="confstrs[fPayolutionMinInstallment]" value="[{$confstrs.fPayolutionMinInstallment}]">
            </td>
            <td class="infoblock">
                [{oxinputhelp ident="HELP_PAYOLUTION_CONFIG_INSTALLMENT_MIN" }]
            </td>
        </tr>

        <tr>
            <td class="w340">
                [{oxmultilang ident="PAYOLUTION_CONFIG_INSTALLMENT_MAX"}]
            </td>
            <td class="w290">
                <input type="text" class="normal payolution-inside-shadow w250" name="confstrs[fPayolutionMaxInstallment]" value="[{$confstrs.fPayolutionMaxInstallment}]">
            </td>
            <td class="infoblock">
                [{oxinputhelp ident="HELP_PAYOLUTION_CONFIG_INSTALLMENT_MAX" }]
            </td>
        </tr>

        <tr>
            <td class="w340">
                [{oxmultilang ident="PAYOLUTION_CONFIG_BASE64_ENCODED_COMPANY_NAME"}]
            </td>
            <td class="w290">
                <input type="text" class="normal payolution-inside-shadow w250" name="confstrs[sBase64EncodedCompanyName]" value="[{$confstrs.sBase64EncodedCompanyName}]">
            </td>
            <td class="infoblock">
                [{oxinputhelp ident="HELP_PAYOLUTION_CONFIG_BASE64_ENCODED_COMPANY_NAME" }]
            </td>
        </tr>
    </table>
</div>

[{*bank details*}]
<div id="bankDetails" class="payolution-country-row">
    <div class="row">
        <div class="sign plus"></div>
        <div class="text">
            [{oxmultilang ident="PAYOLUTION_BANK_DETAILS_TITLE"}]
        </div>
    </div>
    <div class="clear"></div>
    <table class="country-options" style="display: none;">
        <tr>
            <td class="w340">
                [{oxmultilang ident="PAYOLUTION_BANK_DETAILS_COUNTRY"}]
            </td>
            <td class="w290">
                <select id="countrySelector" name="activeCountries" class="normalSelect w272">
                    <option value="">[{oxmultilang ident="PAYOLUTION_BANK_DETAILS_DEFAULT"}]</option>
                    [{foreach from=$activeCountries item=item}]
                    <option value="[{$item.OXISOALPHA2}]">[{$item.OXTITLE}]</option>
                    [{/foreach}]

                </select>
            </td>
            <td class="infoblock">
                [{oxinputhelp ident="PAYOLUTION_BANK_DETAILS_COUNTRY_HELP" }]
            </td>
        </tr>
        <tr>
            <td class="w340">
                [{oxmultilang ident="PAYOLUTION_BANK_DETAILS_RECEIVER" }]
            </td>
            <td class="w290">
                <input type="text" class="normal payolution-inside-shadow w250 country default" name="confstrs[sBankReceiver]" value="[{$confstrs.sBankReceiver}]">
                [{foreach from=$activeCountries item=item}]
                [{assign var=value value=sBankReceiver_`$item.OXISOALPHA2`}]
                <input type="text" class="normal payolution-inside-shadow w250 country [{$item.OXISOALPHA2}]" style="display: none" data-country="[{$item.OXISOALPHA2}]"
                       name="confstrs[sBankReceiver_[{$item.OXISOALPHA2}]]" value="[{$confstrs.$value}]">
                [{/foreach}]
            </td>
            <td class="infoblock">
                [{oxinputhelp ident="PAYOLUTION_BANK_DETAILS_RECEIVER_HELP" }]
            </td>
        </tr>
        <tr>
            <td class="w340">
                [{oxmultilang ident="PAYOLUTION_BANK_DETAILS_IBAN" }]
            </td>
            <td class="w290">
                <input type="text" class="normal payolution-inside-shadow w250 country default" name="confstrs[sBankIBAN]" value="[{$confstrs.sBankIBAN}]">
                [{foreach from=$activeCountries item=item}]
                [{assign var=value value=sBankIBAN_`$item.OXISOALPHA2`}]
            <input type="text" class="normal payolution-inside-shadow w250 country [{$item.OXISOALPHA2}]" style="display: none"
                   name="confstrs[sBankIBAN_[{$item.OXISOALPHA2}]]" value="[{$confstrs.$value}]">
                [{/foreach}]
            </td>
            <td class="infoblock">
                [{oxinputhelp ident="PAYOLUTION_BANK_DETAILS_IBAN_HELP" }]
            </td>
        </tr>
        <tr>
            <td class="w340">
                [{oxmultilang ident="PAYOLUTION_BANK_DETAILS_BIC" }]
            </td>
            <td class="w290">
                <input type="text" class="normal payolution-inside-shadow w250 country default" name="confstrs[sBankBIC]" value="[{$confstrs.sBankBIC}]">
                [{foreach from=$activeCountries item=item}]
                [{assign var=value value=sBankBIC_`$item.OXISOALPHA2`}]
                <input type="text" class="normal payolution-inside-shadow w250 country [{$item.OXISOALPHA2}]" style="display: none" data-country="[{$item.OXISOALPHA2}]"
                   name="confstrs[sBankBIC_[{$item.OXISOALPHA2}]]" value="[{$confstrs.$value}]">
                [{/foreach}]
            </td>
            <td class="infoblock">
                [{oxinputhelp ident="PAYOLUTION_BANK_DETAILS_BIC_HELP" }]
            </td>
        </tr>
    </table>
</div>

<input type="submit" name="save" value="[{oxmultilang ident="GENERAL_SAVE"}]" class="payolution-blue-button corners mgt20" onClick="Javascript:document.myedit.fnc.value='save'" [{$readonly}]>
</form>
</div>
</div>
