[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]
[{assign var="sPjQuery" value=$oViewConf->getPayolutionModuleUrl('out/src/js/libs/payolution.jquery.min.js')}]
[{assign var="sjQuery" value=$oViewConf->getPayolutionModuleUrl('out/src/js/libs/jquery.min.js')}]
[{assign var="sJsControl" value=$oViewConf->getPayolutionModuleUrl('out/src/js/payo-admin-order.js')}]
[{assign var="payoAdminCss" value=$oViewConf->getPayolutionModuleUrl('out/admin/src/css/payolution_admin.css')}]
[{assign var="payoLogo" value=$oViewConf->getPayolutionModuleUrl('out/admin/src/images/payolution-logo.png')}]
[{assign var="historyLogo" value=$oViewConf->getPayolutionModuleUrl('out/admin/src/images/history.png')}]

<link rel="stylesheet" type="text/css" href="[{$payoAdminCss}]"/>

[{assign var="payolution" value=$oViewConf->getPayolutionModule()}]

[{if $readonly}]
    [{assign var="readonly" value="readonly disabled"}]
[{else}]
    [{assign var="readonly" value=""}]
[{/if}]
[{if $successMessage}]
    <div class="errorbox successbox">
        <p>[{$successMessage}]</p>
    </div>
[{/if}]

[{assign var="module" value=$oViewConf->getPayolutionModule()}]
[{assign var="curr" value=$edit->oxorder__oxcurrency->value }]
[{assign var="currObject" value=$oViewConf->getCurrencyFromName($curr)}]
[{assign var="sessionCurrency" value=$currObject|@json_encode}]

<img class="payo-logo" src="[{$payoLogo}]" alt="Payolution" width="254" height="61"/>
<div class="clear"></div>

<div class="payo-wrapper">
    <form name="transfer" id="transfer" action="[{ $oViewConf->getSelfLink() }]" method="post">
        [{ $oViewConf->getHiddenSid() }]
        <input type="hidden" name="oxid" value="[{ $oxid }]">
        <input type="hidden" name="cl" value="payolution_orders">
    </form>
    [{if $payolution->isPayolutionOrder($edit)}]
        [{assign var="payolutionOrder" value=$payolution->asPayolutionOrder($edit)}]
        [{assign var="context" value=$payolutionOrder->orderingContext()}]
        [{assign var="paymentOptions" value=$context->paymentOptions()}]
        [{assign var="basket" value=$context->basket()}]
        [{assign var="price" value=$basket->totalOrderPrice()}]

        <form name="myedit0" id="myedit0" action="[{ $oViewConf->getSelfLink() }]" method="post">
            [{ $oViewConf->getHiddenSid() }]
            <input type="hidden" name="cl" value="payolution_orders">
            <input type="hidden" name="fnc" value="">
            <input type="hidden" name="oxid" value="[{ $oxid }]">
            <input type="hidden" name="invoiceno" value="[{ $edit->oxorder__poinvoiceno->value}]">

            [{if !$payolutionOrder->isInstallmentOrder() && !$payolutionOrder->isShipped() && $payolutionOrder->hasUnshippedItems($partialItems)}]
                <span class="payo-title">[{oxmultilang ident="PAYOLUTION_ORDERS_TRACK_YOUR_ORDER"}]</span>
                <hr/>
                [{if !$payolutionOrder->isCanceled()}]
                    [{if count($partialItems) == 0}]
                        <button id="fullCancel" type="button" onClick="Javascript:document.myedit0.fnc.value='cancelOrder'">[{oxmultilang ident="PAYOLUTION_ORDERS_CANCEL"}]</button>
                    [{/if}]
                    <button id="fullDelivery" type="button"  onClick="Javascript:document.myedit0.fnc.value='shipOrder'">[{oxmultilang ident="PAYOLUTION_ORDERS_SHIP"}]</button>
                [{/if}]
                [{if $oView->isOrderPdfEnabled()}]
                    <button id="invoicePdf" type="button" class="payo-grey payo-right">[{oxmultilang ident="PAYOLUTION_ORDERS_PDF"}]</button>
                    [{assign var="pdfShown" value=true}]
                [{/if}]
                <div style="clear:both"></div>

                <table cellspacing="0" cellpadding="0" border="0" width="100%" id="payoOrderArticles">
                    <tr>
                        <td class="listheader first">&nbsp;</td>
                        <td class="listheader">&nbsp;&nbsp;&nbsp;[{ oxmultilang ident="PAYOLUTION_ORDERS_QTY" }]</td>
                        <td class="listheader">&nbsp;&nbsp;&nbsp;[{ oxmultilang ident="GENERAL_ITEMNR" }]</td>
                        <td class="listheader">&nbsp;&nbsp;&nbsp;[{ oxmultilang ident="GENERAL_TITLE" }]</td>
                        <td class="listheader">&nbsp;&nbsp;&nbsp;[{ oxmultilang ident="GENERAL_TYPE" }]</td>
                        <td class="listheader">&nbsp;&nbsp;&nbsp;[{ oxmultilang ident="ORDER_ARTICLE_PARAMS" }]</td>
                        <td class="listheader">[{ oxmultilang ident="PAYOLUTION_ORDERS_NETTO" }]</td>
                        <td class="listheader">[{ oxmultilang ident="PAYOLUTION_ORDERS_BRUTTO" }]</td>
                        <td class="listheader">[{ oxmultilang ident="GENERAL_ATALL" }]</td>
                        <td class="listheader">[{ oxmultilang ident="ORDER_ARTICLE_MWST" }]</td>
                    </tr>
                    [{assign var="blWhite" value=""}]
                    [{foreach from=$edit->getOrderArticles() item=oOrderArticle name=orderArticles}]
                        [{assign var="itemId" value=$oOrderArticle->oxorderarticles__oxid->value}]
                        [{if isset($partialItems.$itemId)}]
                            [{assign var="amount" value=$oOrderArticle->oxorderarticles__oxamount->value-$partialItems.$itemId}]
                        [{else}]
                            [{assign var="amount" value=$oOrderArticle->oxorderarticles__oxamount->value}]
                        [{/if}]
                        [{if $amount==0}]
                            [{php}]continue;[{/php}]
                        [{/if}]
                        <tr id="art.[{$smarty.foreach.orderArticles.iteration}]">
                            [{if $oOrderArticle->oxorderarticles__oxstorno->value == 1 }]
                                [{assign var="listclass" value=listitem3 }]
                            [{else}]
                                [{assign var="listclass" value=listitem$blWhite }]
                            [{/if}]
                            <td valign="top" align="center" class="[{ $listclass}]">
                                [{assign var="itemId" value=$oOrderArticle->oxorderarticles__oxid->value}]
                                <input type="checkbox" value="1" [{if $payolutionOrder->isCanceled()}]disabled="disabled"[{/if}] class="payo-order-article-check delivery" name="orderArticle[[{$oOrderArticle->oxorderarticles__oxid->value}]]" data-id="[{$oOrderArticle->oxorderarticles__oxid->value}]" />
                            </td>
                            <td valign="top" class="[{ $listclass}]">
                                [{assign var="Price" value=$oOrderArticle->getPrice()}]
                                <input type="text" data-original-amount="[{$amount}]" data-price="[{$Price->getPrice()}]" class="amount payo-field" value="[{$amount}]" name="articleAmount[[{$oOrderArticle->oxorderarticles__oxid->value}]]" style="display: none"/>
                                <span class="payo-amount">[{$amount}]</span>
                            </td>
                            <td valign="top" class="[{ $listclass}]">[{ $oOrderArticle->oxorderarticles__oxartnum->value }]</td>
                            <td valign="top" class="[{ $listclass}]">[{ $oOrderArticle->oxorderarticles__oxtitle->value|oxtruncate:20:""|strip_tags }]</td>
                            <td valign="top" class="[{ $listclass}]">[{ $oOrderArticle->oxorderarticles__oxselvariant->value }]</td>
                            <td valign="top" class="[{ $listclass}]">
                                [{if $oOrderArticle->getPersParams() }]
                                    [{foreach key=sVar from=$oOrderArticle->getPersParams() item=aParam}]
                                        &nbsp;&nbsp;,&nbsp;<em>[{$sVar}] : [{$aParam}]</em>
                                    [{/foreach}]
                                [{/if}]
                            </td>
                            <td valign="top" class="[{ $listclass}]">[{ $oOrderArticle->getNetPriceFormated() }] <small>[{ $edit->oxorder__oxcurrency->value }]</small></td>
                            <td valign="top" class="[{ $listclass}]">[{ $oOrderArticle->getBrutPriceFormated() }] <small>[{ $edit->oxorder__oxcurrency->value }]</small></td>
                            <td valign="top" class="[{ $listclass}]">[{ $oOrderArticle->getTotalBrutPriceFormated($amount) }] <small>[{ $edit->oxorder__oxcurrency->value }]</small></td>
                            <td valign="top" class="[{ $listclass}]">[{ $oOrderArticle->oxorderarticles__oxvat->value}]</td>
                        </tr>
                        [{if $blWhite == "2"}]
                            [{assign var="blWhite" value=""}]
                        [{else}]
                            [{assign var="blWhite" value="2"}]
                        [{/if}]
                    [{/foreach}]
                </table>
                <input type="submit" id="partialDelivery" class="edittext payo-right" name="partialShip" value="[{oxmultilang ident="PAYOLUTION_ORDERS_PARTIAL_SHIP"}]" onClick="Javascript:document.myedit0.fnc.value='shipOrder'"/><br/>
                <br>
            [{/if}]

            [{if $payolutionOrder->isInstallmentOrder() || (!$payolutionOrder->isCreated() && !$payolutionOrder->isCanceled()) }]
                <span class="payo-title">[{oxmultilang ident="PAYOLUTION_ORDERS_REFUND"}]</span>
                <hr/>
                [{if !$payolutionOrder->isCanceled()}]
                    <b>[{oxmultilang ident="PAYOLUTION_ORDERS_REFUND_AMOUNT"}]:</b>
                    <div class="payo-field">
                        <input type="text" name="refundAmount" id="refundAmount" value="[{$payolutionOrder->getFormattedAvailableRefundAmount()}]" data-original-amount="[{$payolutionOrder->getFormattedAvailableRefundAmount()}]" data-current-amount="[{$payolutionOrder->getFormattedAvailableRefundAmount()}]"/>
                        <span class="payo-field-symbol">[{ $edit->oxorder__oxcurrency->value }]</span>
                    </div>
                    <input type="text" name="tabStop" style="border: none; width: 0; margin: 0; padding: 0">
                    <b>=</b>
                    <div class="payo-field">
                        <input type="text" id="refundPercentage" value="100"/>
                        <span class="payo-field-symbol">%</span>
                    </div>
                    [{if $payolutionOrder->availableRefundAmount() eq "0.00"}]
                        <button disabled="disabled" id="fullRefund" type="button" class="payolution-button blue" onClick="Javascript:document.myedit0.fnc.value='refundOrder'">[{oxmultilang ident="PAYOLUTION_ORDERS_REFUND"}]</button>
                    [{else}]
                        <button id="fullRefund" type="button" class="payolution-button blue" onClick="Javascript:document.myedit0.fnc.value='refundOrder'">[{oxmultilang ident="PAYOLUTION_ORDERS_REFUND"}]</button>
                    [{/if}]
                [{/if}]
                [{if $oView->isOrderPdfEnabled() && !isset($pdfShown)}]
                    <button id="invoicePdf" type="button" class="payo-grey payo-right">[{oxmultilang ident="PAYOLUTION_ORDERS_PDF"}]</button>
                [{/if}]
                <div style="clear:both"></div>


                <table cellspacing="0" cellpadding="0" border="0" width="100%" id="payoOrderArticles">
                    <tr>
                        <td class="listheader first">&nbsp;</td>
                        <td class="listheader">&nbsp;&nbsp;&nbsp;[{ oxmultilang ident="PAYOLUTION_ORDERS_QTY" }]</td>
                        <td class="listheader">&nbsp;&nbsp;&nbsp;[{ oxmultilang ident="GENERAL_ITEMNR" }]</td>
                        <td class="listheader">&nbsp;&nbsp;&nbsp;[{ oxmultilang ident="GENERAL_TITLE" }]</td>
                        <td class="listheader">&nbsp;&nbsp;&nbsp;[{ oxmultilang ident="GENERAL_TYPE" }]</td>
                        <td class="listheader">&nbsp;&nbsp;&nbsp;[{ oxmultilang ident="ORDER_ARTICLE_PARAMS" }]</td>
                        <td class="listheader">[{ oxmultilang ident="PAYOLUTION_ORDERS_NETTO" }]</td>
                        <td class="listheader">[{ oxmultilang ident="PAYOLUTION_ORDERS_BRUTTO" }]</td>
                        <td class="listheader">[{ oxmultilang ident="GENERAL_ATALL" }]</td>
                        <td class="listheader">[{ oxmultilang ident="ORDER_ARTICLE_MWST" }]</td>
                    </tr>
                    [{assign var="blWhite" value=""}]
                    [{foreach from=$edit->getOrderArticles() item=oOrderArticle name=orderArticles}]
                        [{assign var="itemId" value=$oOrderArticle->oxorderarticles__oxid->value}]
                        [{if isset($partialItems.$itemId)}]
                            [{assign var="amount" value=$partialItems.$itemId}]
                        [{else}]
                            [{php}]continue;[{/php}]
                        [{/if}]
                        <tr id="art.[{$smarty.foreach.orderArticles.iteration}]">
                            [{if $oOrderArticle->oxorderarticles__oxstorno->value == 1 }]
                                [{assign var="listclass" value=listitem3 }]
                            [{else}]
                                [{assign var="listclass" value=listitem$blWhite }]
                            [{/if}]
                            <td valign="top" align="center" class="[{ $listclass}]">
                                <input type="checkbox" value="1" [{if $payolutionOrder->isCanceled()}]disabled="disabled"[{/if}] class="payo-order-article-check" name="orderArticleRef[[{$oOrderArticle->oxorderarticles__oxid->value}]]" data-id="[{$oOrderArticle->oxorderarticles__oxid->value}]" />
                            </td>
                            <td valign="top" class="[{ $listclass}]">
                                [{assign var="Price" value=$oOrderArticle->getPrice()}]
                                <input type="text" data-original-amount="[{$amount}]" data-price="[{$Price->getPrice()}]" class="amount amountRef payo-field" value="[{$amount}]" name="articleAmountRef[[{$oOrderArticle->oxorderarticles__oxid->value}]]" style="display: none"/>
                                <span class="payo-amount">[{$amount}]</span>
                            </td>
                            <td valign="top" class="[{ $listclass}]">[{ $oOrderArticle->oxorderarticles__oxartnum->value }]</td>
                            <td valign="top" class="[{ $listclass}]">[{ $oOrderArticle->oxorderarticles__oxtitle->value|oxtruncate:20:""|strip_tags }]</td>
                            <td valign="top" class="[{ $listclass}]">[{ $oOrderArticle->oxorderarticles__oxselvariant->value }]</td>
                            <td valign="top" class="[{ $listclass}]">
                                [{if $oOrderArticle->getPersParams() }]
                                    [{foreach key=sVar from=$oOrderArticle->getPersParams() item=aParam}]
                                        &nbsp;&nbsp;,&nbsp;<em>[{$sVar}] : [{$aParam}]</em>
                                    [{/foreach}]
                                [{/if}]
                            </td>
                            <td valign="top" class="[{ $listclass}]">[{ $oOrderArticle->getNetPriceFormated() }] <small>[{ $edit->oxorder__oxcurrency->value }]</small></td>
                            <td valign="top" class="[{ $listclass}]">[{ $oOrderArticle->getBrutPriceFormated() }] <small>[{ $edit->oxorder__oxcurrency->value }]</small></td>
                            <td valign="top" class="[{ $listclass}]">[{ $oOrderArticle->getTotalBrutPriceFormated($amount) }] <small>[{ $edit->oxorder__oxcurrency->value }]</small></td>
                            <td valign="top" class="[{ $listclass}]">[{ $oOrderArticle->oxorderarticles__oxvat->value}]</td>
                        </tr>
                        [{if $blWhite == "2"}]
                            [{assign var="blWhite" value=""}]
                        [{else}]
                            [{assign var="blWhite" value="2"}]
                        [{/if}]
                    [{/foreach}]
                </table>
            [{/if}]
            <hr><br><br>
        </form>

        [{if $payolutionOrder->isInstallmentOrder()}]
            <br/>
            <br/>
            <span class="payo-title">[{oxmultilang ident="PAYOLUTION_ORDERS_INSTALLMENT_PERIOD"}]</span>
            <hr/>
            [{oxmultilang ident="PAYOLUTION_ORDERS_NUMBER_OF_MONTHLY_RATES"}]: <b>[{$paymentOptions.payolution_installment_period}]</b>
        [{/if}]

        <br/>
        <br/>
        <br/>
        <br/>
        <span class="payo-title">[{oxmultilang ident="PAYOLUTION_ORDERS_HISTORY"}]</span>
        <hr/>
        <img id="payo-history-logo" src="[{$historyLogo}]" alt="[{oxmultilang ident="PAYOLUTION_ORDERS_HISTORY"}]" width="64" height="62"/>
        <table id="payo-history">
            [{foreach from=$history item=item}]
                <tr>
                    <td>[{$item.date}]</td>
                    <td>[{$item.time}]</td>
                    <td>[{$item.status}]</td>
                </tr>
            [{/foreach}]
        </table>
    [{else}]
        [{ oxmultilang ident="PAYOLUTION_ORDERS_NOT_PAYOLUTION_ORDER" }]
    [{/if}]
    [{include file="bottomitem.tpl"}]
    <script type="text/javascript" src="[{$sPjQuery}]"></script>
    <script type="text/javascript" src="[{$sjQuery}]"></script>
    <script type="text/javascript" src="[{$sJsControl}]"></script>
    <script type="text/javascript" src="[{$oViewConf->getPayolutionModuleUrl("out/src/js/libs/accounting.js")}]"></script>
    <script type="text/javascript">
        var sessionCurrency=[{$sessionCurrency}];
    </script>
</div>
