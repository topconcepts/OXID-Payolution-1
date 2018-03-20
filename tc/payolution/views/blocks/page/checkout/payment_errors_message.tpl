[{assign var="iPayError" value=$oView->getPaymentError() }]
[{if $iPayError == -200}] [{* Payolution_Error::REJECTED *}]
    <div class="status error">[{ oxmultilang ident="PAYOLUTION_ERROR_REQUEST_WAS_REJECTED_ON_PRECHECK" }]</div>
[{elseif $iPayError == -201}] [{* Payolution_Error::VALIDATION_REQUIRED_FIELDS_IS_MISSING *}]
    <div class="status error">[{ oxmultilang ident="PAYOLUTION_ERROR_MISSING_REQUIRED_FIELDS" }]</div>
[{elseif $iPayError == -202}] [{* Payolution_Error::VALIDATION_AGE_RESTRICTION *}]
    <div class="status error">[{ oxmultilang ident="PAYOLUTION_ERROR_AGE_RESTRICTION" }]</div>
[{elseif $iPayError == -203}] [{* Payolution_Error::VALIDATION_ADDRESS *}]
    <div class="status error">[{ oxmultilang ident="PAYOLUTION_ERROR_ADDRESSES_IS_NOT_THE_SAME" }]</div>
[{elseif $iPayError == -204}] [{* Payolution_Error::VALIDATION_TERMS_AND_CONDITIONS *}]
    <div class="status error">[{ oxmultilang ident="PAYOLUTION_ERROR_TERMS_AND_CONDITIONS_HAS_NOT_AGREED" }]</div>
[{elseif $iPayError == -205}] [{* Payolution_Error::ORDER_STATUS_TRANSITION_NOT_ALLOWED *}]
<div class="status error">[{ oxmultilang ident="PAYOLUTION_ERROR_ORDER_STATUS_TRANSITION_NOT_ALLOWED" }]</div>
[{elseif $iPayError == -206}] [{* Payolution_Error::PAYOLUTION_REMOTE_ERROR_RESPONSE *}]
    <div class="status error">[{oxmultilang ident="PAYOLUTION_ERROR_REMOTE_ERROR_RESPONSE"}]</div>
[{/if}]
