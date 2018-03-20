[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE_1"|oxmultilangassign skip_onload="true"}]
[{assign var="sCssPath" value=$oViewConf->getPayolutionModuleUrl('out/admin/src/css/payolution_admin.css')}]
<link rel="stylesheet" href="[{$sCssPath}]">
<div class="main-container">
    <div class="payolution-intro">
        <div class="payolution-intro-text">
            <h1 class="payolution">[{oxmultilang ident="payolutionexpert"}]</h1>
            <p class="mg0">
                [{oxmultilang ident="EXPERT_SETTINGS_DESCRIPTION"}]
            </p>
        </div>
        <div class="payolution-logo"></div>
        <div class="clear"></div>
    </div>

    <form name="myedit" id="myedit" method="post" action="[{$oViewConf->getSelfLink()}]" enctype="multipart/form-data">
        <input type="hidden" name="MAX_FILE_SIZE" value="[{$iMaxUploadFileSize}]">
        [{$oViewConf->getHiddenSid()}]
        <input type="hidden" name="cl" value="payolution_expert">
        <input type="hidden" name="fnc" value="">
        <table class="payolution-expert">
            <tr>
                <td>
                   <div class="payolution-expert-title">
                       <span class="left">[{oxmultilang ident="PAYOLUTION_CONFIG_COUNTRIES"}]</span>
                       [{oxinputhelp ident="HELP_PAYOLUTION_CONFIG_COUNTRIES"}]
                   </div>
                   <textarea class="txtfield payolution-inside-shadow" name=confaarrs[aPayolutionCountries] [{$readonly}]>[{$confaarrs.aPayolutionCountries}]</textarea>
                </td>
                <td>
                    <div class="payolution-expert-title">
                        <span class="left">[{oxmultilang ident="PAYOLUTION_CONFIG_CURRENCY" }]</span>
                        [{oxinputhelp ident="HELP_PAYOLUTION_CONFIG_CURRENCY" }]
                    </div>
                    <textarea class="txtfield payolution-inside-shadow" name=confaarrs[aPayolutionCurrency] [{$readonly}]>[{$confaarrs.aPayolutionCurrency}]</textarea>
                </td>
                <td>
                    <div class="payolution-expert-title">
                        <span class="left">[{oxmultilang ident="PAYOLUTION_CONFIG_LANGUAGE"}]</span>
                        [{oxinputhelp ident="HELP_PAYOLUTION_CONFIG_LANGUAGE"}]
                    </div>
                    <textarea class="txtfield payolution-inside-shadow" name=confaarrs[aPayolutionLanguage] [{$readonly}]>[{$confaarrs.aPayolutionLanguage}]</textarea>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="payolution-expert-title">
                        <span class="left">[{oxmultilang ident="PAYOLUTION_CONFIG_COUNTRY2LANGUAGE" }]</span>
                        [{oxinputhelp ident="HELP_PAYOLUTION_CONFIG_COUNTRY2LANGUAGE" }]
                    </div>
                    <textarea class="txtfield payolution-inside-shadow" name=confaarrs[aPayolutionCountry2Language] [{$readonly}]>[{$confaarrs.aPayolutionCountry2Language}]</textarea>
                </td>
                <td>
                   <div class="payolution-expert-title">
                       <span class="left">[{oxmultilang ident="PAYOLUTION_CONFIG_COUNTRY2LOCALE"}]</span>
                       [{oxinputhelp ident="HELP_PAYOLUTION_CONFIG_COUNTRY2LOCALE"}]
                   </div>
                   <textarea class="txtfield payolution-inside-shadow" name=confaarrs[aPayolutionCountry2Locale] [{$readonly}]>[{$confaarrs.aPayolutionCountry2Locale}]</textarea>
                </td>
                <td>
                    <div class="payolution-expert-title">
                        <span class="left">[{oxmultilang ident="PAYOLUTION_CONFIG_COUNTRY2CURRENCY"}]</span>
                        [{oxinputhelp ident="HELP_PAYOLUTION_CONFIG_COUNTRY2CURRENCY"}]
                    </div>
                    <textarea class="txtfield payolution-inside-shadow" name=confaarrs[aPayolutionCountry2Currency] [{$readonly}]>[{$confaarrs.aPayolutionCountry2Currency}]</textarea>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="payolution-expert-title">
                        <span class="left">[{oxmultilang ident="PAYOLUTION_CONFIG_COUNTRY2PNOENCODING"}]</span>
                        [{oxinputhelp ident="HELP_PAYOLUTION_CONFIG_COUNTRY2PNOENCODING"}]
                    </div>
                    <textarea class="txtfield payolution-inside-shadow" name=confaarrs[aPayolutionCountry2PNOEncoding] [{$readonly}]>[{$confaarrs.aPayolutionCountry2PNOEncoding}]</textarea>
                </td>
                <td>
                    <div class="payolution-expert-title">
                        <span class="left">[{oxmultilang ident="PAYOLUTION_CONFIG_GENDER2GENDER"}]</span>
                        [{oxinputhelp ident="HELP_PAYOLUTION_CONFIG_GENDER2GENDER"}]
                    </div>
                    <textarea class="txtfield payolution-inside-shadow" name=confaarrs[aPayolutionGender2Gender] [{$readonly}]>[{$confaarrs.aPayolutionGender2Gender}]</textarea>
                </td>
                <td>
                </td>
            </tr>
        </table>
        <input type="submit" name="save" class="payolution-blue-button corners" value="[{oxmultilang ident="GENERAL_SAVE"}]" onClick="Javascript:document.myedit.fnc.value='save'" [{$readonly}]>
    </form>
</div>
