[{include file="headitem.tpl" title="NEWS_LIST_TITLE"|oxmultilangassign box="list"}]
[{assign var="where" value=$oView->getListFilter()}]

[{if $readonly}]
    [{assign var="readonly" value="readonly disabled"}]
[{else}]
    [{assign var="readonly" value=""}]
[{/if}]

<script type="text/javascript">
<!--
window.onload = function ()
{
    top.reloadEditFrame();
    [{ if $updatelist == 1}]
        top.oxid.admin.updateList('[{ $oxid }]');
    [{ /if}]
}
//-->
</script>

<div id="liste">

<form name="search" id="search" action="[{ $oViewConf->getSelfLink() }]" method="post">
[{include file="_formparams.tpl" cl="payolution_apiloglist" lstrt=$lstrt actedit=$actedit oxid=$oxid fnc="" language=$actlang editlanguage=$actlang}]
<table cellspacing="0" cellpadding="0" border="0" width="100%">
    <colgroup>
        [{block name="admin_payolution_apilog_list_colgroup"}]
            <col width="10%">
            <col width="30%">
            <col width="30%">
            <col width="30%">
        [{/block}]
    </colgroup>
    <tr class="listitem">
        [{block name="admin_payolution_apilog_list_filter"}]
            <td colspan="4" valign="top" class="listfilter" height="20"></td>
        [{/block}]
    </tr>
    <tr>
        [{block name="admin_payolution_apilog_list_sorting"}]
            <td class="listheader first" height="15">[{ oxmultilang ident="GENERAL_DATE" }]</td>
            <td class="listheader">[{ oxmultilang ident="PAYOLUTION_ORDER_TITLE" }]</td>
            <td class="listheader">[{ oxmultilang ident="PAYOLUTION_REQUEST_TITLE" }]</td>
            <td class="listheader">[{ oxmultilang ident="PAYOLUTION_RESPONSE_TITLE" }]</td>
        [{/block}]
    </tr>

[{assign var="blWhite" value=""}]
[{assign var="_cnt" value=0}]
[{foreach from=$mylist item=listitem}]
    [{assign var="_cnt" value=$_cnt+1}]
    <tr id="row.[{$_cnt}]">

        [{block name="admin_payolution_apilog_list_item"}]
            [{ if $listitem->blacklist == 1}]
                [{assign var="listclass" value=listitem3 }]
            [{ else}]
                [{assign var="listclass" value=listitem$blWhite }]
            [{ /if}]
            [{ if $listitem->getId() == $oxid }]
                [{assign var="listclass" value=listitem4 }]
            [{ /if}]
            <td valign="top" class="[{ $listclass}]" height="15"><div class="listitemfloating">&nbsp;<a href="Javascript:top.oxid.admin.editThis('[{ $listitem->payo_logs__oxid->value}]');" class="[{ $listclass}]">[{ $listitem->payo_logs__added_at|oxformdate }]</a></div></td>
            <td valign="top" class="[{ $listclass}]"><div class="listitemfloating"><a href="Javascript:top.oxid.admin.editThis('[{ $listitem->payo_logs__oxid->value }]');" class="[{ $listclass}]">[{ $listitem->payo_logs__order_no->value }]</a></div></td>
            <td valign="top" class="[{ $listclass}]"><div class="listitemfloating"><a href="Javascript:top.oxid.admin.editThis('[{ $listitem->payo_logs__oxid->value }]');" class="[{ $listclass}]">[{ $listitem->payo_logs__request->value|truncate:100 }]</a></div></td>
            <td valign="top" class="[{ $listclass}]"><div class="listitemfloating"><a href="Javascript:top.oxid.admin.editThis('[{ $listitem->payo_logs__oxid->value }]');" class="[{ $listclass}]">[{ $listitem->payo_logs__response->value|truncate:100 }]</a></div></td>

        [{/block}]
    </tr>
[{if $blWhite == "2"}]
[{assign var="blWhite" value=""}]
[{else}]
[{assign var="blWhite" value="2"}]
[{/if}]
[{/foreach}]
[{include file="pagenavisnippet.tpl" colspan="4"}]
</table>
</form>
</div>

[{include file="pagetabsnippet.tpl"}]


<script type="text/javascript">
if (parent.parent)
{   parent.parent.sShopTitle   = "[{$actshopobj->oxshops__oxname->getRawValue()|oxaddslashes}]";
    parent.parent.sMenuItem    = "[{ oxmultilang ident="NEWS_LIST_MENUITEM" }]";
    parent.parent.sMenuSubItem = "[{ oxmultilang ident="NEWS_LIST_MENUSUBITEM" }]";
    parent.parent.sWorkArea    = "[{$_act}]";
    parent.parent.setTitle();
}
</script>
</body>
</html>
