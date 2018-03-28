[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]

[{if $readonly}]
    [{assign var="readonly" value="readonly disabled"}]
[{else}]
    [{assign var="readonly" value=""}]
[{/if}]

<form name="transfer" id="transfer" action="[{ $oViewConf->getSelfLink() }]" method="post">
    [{ $oViewConf->getHiddenSid() }]
    <input type="hidden" name="oxid" value="[{ $oxid }]">
    <input type="hidden" name="cl" value="payolution_apilogsdetails">
    <input type="hidden" name="editlanguage" value="[{ $editlanguage }]">
</form>

<input type="hidden" name="cl" value="payolution_apilogsdetails">
<input type="hidden" name="fnc" value="">
<input type="hidden" name="oxid" value="[{ $oxid }]">
<input type="hidden" name="voxid" value="[{ $oxid }]">
<input type="hidden" name="editval[payolution_apilogsdetails__oxid]" value="[{ $oxid }]">

    <table cellspacing="0" cellpadding="0" border="0" width="98%" style="table-layout: fixed; height: 100%">
        <tr>
            <td valign="top" class="edittext" width="50%" style="height:100%">
                <textarea style="width: 100%; height: 100%">
                    [{ $oLogEntry->payo_logs__request->value }]
                </textarea>
            </td>
            <td valign="top" class="edittext vr" align="left" width="50%">
                <textarea style="width: 100%; height: 100%">
                    [{ $oLogEntry->payo_logs__response->value }]
                </textarea>
            </td>
        </tr>
    </table>
