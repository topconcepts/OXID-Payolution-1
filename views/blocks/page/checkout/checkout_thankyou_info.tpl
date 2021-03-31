[{ oxmultilang ident="PAYOLUTION_THANK_YOU_FOR_ORDER" }] [{ $oxcmp_shop->oxshops__oxname->value }]. <br>
[{assign var=registerMsg value="PAYOLUTION_REGISTERED_YOUR_ORDER"|oxmultilangassign}]
[{$registerMsg|replace:'%s':$order->oxorder__oxordernr->value}] <br>
[{if !$oView->getMailError() }]
    [{ oxmultilang ident="PAYOLUTION_MESSAGE_YOU_RECEIVED_ORDER_CONFIRM" }]<br>
[{else}]<br>
    [{ oxmultilang ident="PAYOLUTION_MESSAGE_CONFIRMATION_NOT_SUCCEED" }]<br>
[{/if}]
<br>
[{ oxmultilang ident="PAYOLUTION_MESSAGE_WE_WILL_INFORM_YOU" }]<br><br>
