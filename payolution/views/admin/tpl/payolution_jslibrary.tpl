[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]
[{assign var="sCssPath" value=$oViewConf->getPayolutionModuleUrl('out/admin/src/css/payolution_admin.css')}]
<link rel="stylesheet" href="[{$sCssPath}]">
  <form name="transfer" id="transfer" action="[{ $oViewConf->getSelfLink() }]" method="post">
    [{ $oViewConf->getHiddenSid() }]
    <input type="hidden" name="oxid" value="1">
    <input type="hidden" name="cl" value="">

      <div class="main-container">
          [{if $oView->getTemplateParam('bUpdated')}]
          <div class="payolution-version">
              <span></span> [{oxmultilang ident="PAYOLUTION_SUCCESS_JSLIB_UPDATED"}]
          </div>
          [{/if}]
          <div class="payolution-intro">
              <div class="payolution-intro-text">
                  <h1 class="payolution">
                      [{oxmultilang ident="payolutionjslibrary"}]
                  </h1>

                  <p class="mg0">
                      [{oxmultilang ident="PAYOLUTION_JSLIB_DESCRIPTION"}].
                  </p>
                  <p class="mg0">
                      [{if $oView->getTemplateParam('sbLastUpdate')}]
                          [{oxmultilang ident="PAYOLUTION_JSLIB_LASTUPDATE"}] <b>[{$oView->getTemplateParam('sbLastUpdate')}]</b>.
                      [{else}]
                          <b>[{oxmultilang ident="PAYOLUTION_JSLIB_NOFILEYET"}].</b>
                      [{/if}]
                  </p>
              </div>
          </div>

          <input type="button" name="save" value="[{oxmultilang ident="PAYOLUTION_JSLIB_UPDATE"}]" class="payolution-blue-button corners mgt20" onClick="location.href='?stoken=[{php}]echo $_GET['stoken'][{/php}]&cl=payolution_jslibrary&fnc=update'" [{$readonly}]>
      </div>
  </form>
</body>
</html>
