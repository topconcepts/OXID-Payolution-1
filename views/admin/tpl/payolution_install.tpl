[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE_1"|oxmultilangassign skip_onload="true"}]
[{assign var="sCssPath" value=$oViewConf->getPayolutionModuleUrl('out/admin/src/css/payolution_admin.css')}]
<link rel="stylesheet" href="[{$sCssPath}]">
<div class="main-container">
    [{if $oView->getTemplateParam('bModuleInstalled')}]
        <div class="payolution-version">
            <span></span> [{oxmultilang ident="PAYOLUTION_SUCCESS_MODULE_ALREADY_INSTALLED_1"}] [{$oView->getTemplateParam('sPayolutionVersion')}]
        </div>
    [{/if}]

     <div class="payolution-intro">
        <div class="payolution-intro-text">
            <h1 class="payolution">
                [{oxmultilang ident="START_PAGE_TITLE"}]
            </h1>
            <p class="mg0">
                [{oxmultilang ident="START_PAGE_DESCRIPTION"}]
            </p>
        </div>
        <div class="payolution-logo"></div>
        <div class="clear"></div>
    </div>

    <hr class="line"/>

    <h1 class="payolution">
        [{oxmultilang ident="PAYOLUTION_CONTACT_OPTIONS_TITLE"}]
    </h1>
    <table class="payolution-contacts" cellspacing="1" cellpadding="1">
        <tr>
            <th></th>
            <th></th>
            <th></th>
        </tr>
        <tr>
            <td>
                <div class="payolution-flag de"></div>
                <div class="payolution-contact-info">
                    <div class="payolution-contact-title">
                        <div class="payolution-contact-title-small">[{oxmultilang ident="PAYOLUTION_CONTACT_LOCATION"}]</div>
                        <div class="payolution-contact-title-big">[{oxmultilang ident="PAYOLUTION_CONTACT_LOCATION_DE"}]</div>
                    </div>
                    <p><span>[{oxmultilang ident="PAYOLUTION_PHONE"}]</span>[{oxmultilang ident="PAYOLUTION_CONTACT_LOCATION_DE_PHONE"}]</p>
                    <p><span>[{oxmultilang ident="PAYOLUTION_EMAIL"}]</span>[{oxmultilang ident="PAYOLUTION_CONTACT_LOCATION_DE_EMAIL"}]</p>
                </div>
            </td>
            <td>
                <div class="payolution-flag at"></div>
                <div class="payolution-contact-info">
                    <div class="payolution-contact-title">
                        <div class="payolution-contact-title-small">[{oxmultilang ident="PAYOLUTION_CONTACT_LOCATION"}]</div>
                        <div class="payolution-contact-title-big">[{oxmultilang ident="PAYOLUTION_CONTACT_LOCATION_AT"}]</div>
                    </div>
                    <p><span>[{oxmultilang ident="PAYOLUTION_PHONE"}]</span>[{oxmultilang ident="PAYOLUTION_CONTACT_LOCATION_AT_PHONE"}]</p>
                    <p><span>[{oxmultilang ident="PAYOLUTION_EMAIL"}]</span>[{oxmultilang ident="PAYOLUTION_CONTACT_LOCATION_AT_EMAIL"}]</p>
                </div>
            </td>
            <td>
                <div class="payolution-flag ch"></div>
                <div class="payolution-contact-info">
                    <div class="payolution-contact-title">
                        <div class="payolution-contact-title-small">[{oxmultilang ident="PAYOLUTION_CONTACT_LOCATION"}]</div>
                        <div class="payolution-contact-title-big">[{oxmultilang ident="PAYOLUTION_CONTACT_LOCATION_CH"}]</div>
                    </div>
                    <p><span>[{oxmultilang ident="PAYOLUTION_PHONE"}]</span>[{oxmultilang ident="PAYOLUTION_CONTACT_LOCATION_CH_PHONE"}]</p>
                    <p><span>[{oxmultilang ident="PAYOLUTION_EMAIL"}]</span>[{oxmultilang ident="PAYOLUTION_CONTACT_LOCATION_CH_EMAIL"}]</p>
                </div>
            </td>
        </tr>
    </table>

    <div class="payolution-download">
        <div class="ico"></div>
        <div class="text">
            <h2>[{oxmultilang ident="PAYOLUTION_USER_GUIDE"}]</h2>
            <div class="content">
                [{oxmultilang ident="PAYOLUTION_USER_GUIDE_DESCRIPTION"}]
            </div>
            <div class="payolution-button-container">
                <a class="payolution-download-button corners" target="_blank" href="[{$oViewConf->getDownloadLink()}]">
                    [{oxmultilang ident="PAYOLUTION_DOWNLOAD"}]
                </a>
            </div>
            <div class="clear"></div>
        </div>
        <div class="clear"></div>
    </div>
</div>
[{include file="bottomitem.tpl"}]
