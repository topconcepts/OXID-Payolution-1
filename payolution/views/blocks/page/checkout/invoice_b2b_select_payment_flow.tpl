<div class="form payolutionUserDetails payolution_usertext_[{ $sPaymentID }]">
    <div class="row">
        [{include file="inc/select_payment_element.tpl" element=$form->getElement('dynvalue[payolution_b2b_type]')}]
    </div>
    <div class="row">
        [{include file="inc/select_payment_element.tpl" element=$form->getElement('dynvalue[payolution_b2b_name]')}]
    </div>
    <div class="row">
        [{include file="inc/select_payment_element.tpl" element=$form->getElement('dynvalue[payolution_b2b_owner_family]')}]
    </div>
    <div class="row">
        [{include file="inc/select_payment_element.tpl" element=$form->getElement('dynvalue[payolution_b2b_owner_given]')}]
    </div>
    <div class="row">
        [{include file="inc/select_payment_element.tpl" element=$form->getElement('dynvalue[payolution_b2b_ust_id]')}]
    </div>
    <div class="row">
        [{include file="inc/select_payment_element.tpl" element=$form->getElement('dynvalue[payolution_b2b_owner_birthday]')}]
    </div>
    [{if $form->getElement('dynvalue[payolution_b2b_phone]')}]
        <div class="row">
            [{include file="inc/select_payment_element.tpl" element=$form->getElement('dynvalue[payolution_b2b_phone]')}]
        </div>
    [{/if}]
    <div class="row">
        [{include file="inc/select_payment_element.tpl" element=$form->getElement('dynvalue[payolution_b2b_privacy]')}]
    </div>
</div>