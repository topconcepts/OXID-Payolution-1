<div class="payolution-select-payment-element [{if $element->required()}]required-field[{/if}] col-xs-12 form-group" >
    [{if $element->type() == 'checkbox' }]
        <div class="col-sm-3"></div>
        <div class="col-sm-9">
            [{assign var="value" value=$element->value()}]
            <input type="hidden" name="[{$element->name()}]" value="0"/>
            <input style="float: left" name="[{$element->name()}]" id="[{$element->name()}]" class="js-oxPayolutionValidate js-oxPayolutionValidate_agree" type="checkbox" value="1" [{if $value }]checked="checked"[{/if}]/>
            <label for="[{$element->name()}]" class="col-xs-11 payoLongCheckText">[{ oxmultilang ident=$element->title() }]</label>
            [{if $element->help()}]
                <input type="button" class="payo-help"/>
                <div class="payo-help-text">[{$oViewConf->translateWithArgs($element->help(), $element->helpArgs())}]</div>
                <div style="clear: both"></div>
            [{/if}]
            <p class="oxValidateError col-xs-12 list-unstyled text-danger" style="display: none; padding: 0;" >
                <span class="js-oxPayolutionError_agree">
                    [{if $element->errorText()}]
                        [{$element->errorText()}]
                    [{else}]
                        [{ oxmultilang ident="EXCEPTION_PAYOLUTION_INPUT_TERMS_AGREEMENT_MUST_BE_CHECKED" }]
                    [{/if}]
                </span>
            </p>
        </div>

    [{elseif $element->type() == 'hidden'}]

     <input type="hidden" name="[{$element->name()}]" value="[{$element->value()}]">

    [{elseif $element->type() == 'select'}]
        <label class="req control-label col-sm-3">[{oxmultilang ident=$element->title()}]</label>
        <div class="col-sm-9">
            <select class="form-control selectpicker bs-select-hidden" name="[{$element->name()}]" data-options="[{$element->dataValues()|escape}]">
                [{foreach from=$element->value() item=selectItem }]
                <option [{if $selectItem->isSelected()}]selected[{/if}] value="[{$selectItem->getValue()}]" >
                    [{oxmultilang ident=$selectItem->getTitle()}]
                </option>
                [{/foreach}]
            </select>
        </div>

    [{elseif $element->type() == 'button' }]
    <label>&nbsp;</label>
    <input type="button" size="20" maxlength="64" name="[{$element->name()}]" value="[{ oxmultilang ident=$element->title() }]">
    [{elseif $element->type() == 'input' }]
    <label class="req control-label col-sm-3">[{ oxmultilang ident=$element->title() }]</label>
    <div class="col-sm-9">
    <input class="form-control js-oxPayolutionValidate [{$element->className()}] [{if !$element->required()}]js-oxPayolutionValidate_allowEmpty[{/if}] textbox" type="text" size="20" maxlength="64" name="[{$element->name()}]" value="[{$element->value()}]">
    [{if $element->help()}]
        <input type="button" class="payo-help"/>
        <div class="payo-help-text">[{$oViewConf->translateWithArgs($element->help(), $element->helpArgs())}]</div>
        <div style="clear: both"></div>
    [{/if}]
    <p class="oxValidateError list-unstyled text-danger" style="display: none">
        <span class="js-oxPayolutionError_notEmpty">[{ oxmultilang ident="EXCEPTION_PAYOLUTION_INPUT_NOTALLFIELDS" }]</span>
    </p>
    </div>
    [{elseif $element->type() == 'phone' }]

    <label class="req control-label col-sm-3">[{ oxmultilang ident=$element->title() }]</label>
    <div class="col-sm-9">
    <input class="js-oxPayolutionValidate js-oxPayolutionValidate_phone form-control textbox" type="text" size="20" maxlength="64" name="[{$element->name()}]" value="[{$element->value()}]">
    [{if $element->help()}]
        <input type="button" class="payo-help"/>
        <div class="payo-help-text">[{$oViewConf->translateWithArgs($element->help(), $element->helpArgs())}]</div>
        <div style="clear: both"></div>
    [{/if}]
    <p class="oxValidateError list-unstyled text-danger" style="display: none">
        <span class="js-oxPayolutionError_notEmpty">[{ oxmultilang ident="EXCEPTION_PAYOLUTION_INPUT_NOTALLFIELDS" }]</span>
        <span class="js-oxPayolutionError_phoneFormat">[{ oxmultilang ident="EXCEPTION_PAYOLUTION_INPUT_INVALID_PHONE_FORMAT" }]</span>
    </p>
        </div>
        <br/>
        <br/>
    [{elseif $element->type() == 'birthday' }]
        <label class="req control-label col-sm-3" >[{ oxmultilang ident=$element->title() }]</label>
        <div class="col-sm-9">
            <input placeholder="[{oxmultilang ident='PAYOLUTION_BIRTHDAY_PATTERN'}]" name="[{$element->name()}]" value="[{$element->value()}]"
                   class="col-xs-3 form-control payolution-form-birthday-input js-oxPayolutionValidate js-oxPayolutionValidate_inlineBirthday [{if !$element->required()}]js-oxPayolutionValidate_allowEmpty[{/if}]">
            <p class="col-xs-12 oxValidateError payolution-oxValidateError-limit-width list-unstyled text-danger" style="display: none; padding: 0">
                <span class="js-oxPayolutionError_notEmpty">[{ oxmultilang ident="EXCEPTION_PAYOLUTION_INPUT_NOTALLFIELDS" }]</span>
                <span class="js-oxPayolutionError_birthdayAllFields">[{ oxmultilang ident="EXCEPTION_PAYOLUTION_INPUT_NO_BIRTHDAY_FIELD" }]</span>
                <span class="js-oxPayolutionError_birthdayIncorect">[{ oxmultilang ident="PAYOLUTION_ERROR_MESSAGE_INCORRECT_BIRTHDAY_DATE" }]</span>
            </p>
        </div>
    [{/if}]
</div>
