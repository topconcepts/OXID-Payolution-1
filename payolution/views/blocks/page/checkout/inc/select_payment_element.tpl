[{if $oViewConf->isActiveThemeFlow() || $oViewConf->isActiveThemeWave()}]
    [{include file="inc/select_payment_element_flow.tpl" element=$element}]
[{else}]
<li class="payolution-select-payment-element [{if $element->required()}]required-field[{/if}] payolution-[{$element->type()}]-margin ">
    [{if $element->type() == 'checkbox' }]
        [{assign var="value" value=$element->value()}]
        <input type="hidden" name="[{$element->name()}]" value="0"/>
    <input name="[{$element->name()}]" id="[{$element->name()}]" class="js-oxPayolutionValidate js-oxPayolutionValidate_agree" type="checkbox" value="1" [{if $value }]checked="checked"[{/if}]/>
        <label for="[{$element->name()}]" class="payoLongCheckText">[{ oxmultilang ident=$element->title() }]</label>
        [{if $element->help()}]
            <input type="button" class="payo-help"/>
            <div class="payo-help-text">[{$oViewConf->translateWithArgs($element->help(), $element->helpArgs())}]</div>
            <div style="clear: both"></div>
        [{/if}]
    <p class="oxValidateError">
            <span class="js-oxPayolutionError_agree">
                [{if $element->errorText()}]
                    [{$element->errorText()}]
                [{else}]
                    [{ oxmultilang ident="EXCEPTION_PAYOLUTION_INPUT_TERMS_AGREEMENT_MUST_BE_CHECKED" }]
                [{/if}]
            </span>
        </p>

    [{elseif $element->type() == 'hidden'}]

     <input type="hidden" name="[{$element->name()}]" value="[{$element->value()}]">

    [{elseif $element->type() == 'select'}]
        <label>[{oxmultilang ident=$element->title()}]</label>
        <select class="form-control" name="[{$element->name()}]" data-options="[{$element->dataValues()|escape}]">
            [{foreach from=$element->value() item=seletItem }]
                <option [{if $seletItem->isSelected()}]selected[{/if}] value="[{$seletItem->getValue()}]" >
                    [{oxmultilang ident=$seletItem->getTitle()}]
                </option>
            [{/foreach}]
        </select>

    [{elseif $element->type() == 'button' }]

    <label>&nbsp;</label>
    <input type="button" size="20" maxlength="64" name="[{$element->name()}]" value="[{ oxmultilang ident=$element->title() }]">
    [{elseif $element->type() == 'input' }]
    <label>[{ oxmultilang ident=$element->title() }]</label>
    <input class="js-oxPayolutionValidate [{$element->className()}] [{if !$element->required()}]js-oxPayolutionValidate_allowEmpty[{/if}] textbox"
           type="text" size="20" maxlength="64" name="[{$element->name()}]" value="[{$element->value()}]">
    [{if $element->help()}]
        <input type="button" class="payo-help"/>
        <div class="payo-help-text">[{$oViewConf->translateWithArgs($element->help(), $element->helpArgs())}]</div>
        <div style="clear: both"></div>
    [{/if}]
        <p class="oxValidateError" style="display: none">
            <span class="js-oxPayolutionError_notEmpty">[{ oxmultilang ident="EXCEPTION_PAYOLUTION_INPUT_NOTALLFIELDS" }]</span>
        </p>

    [{elseif $element->type() == 'phone' }]

    <label>[{ oxmultilang ident=$element->title() }]</label>
    <input class="js-oxPayolutionValidate js-oxPayolutionValidate_phone textbox" type="text" size="20" maxlength="64" name="[{$element->name()}]" value="[{$element->value()}]">
    [{if $element->help()}]
        <input type="button" class="payo-help"/>
        <div class="payo-help-text">[{$oViewConf->translateWithArgs($element->help(), $element->helpArgs())}]</div>
        <div style="clear: both"></div>
    [{/if}]
    <p class="oxValidateError" style="display: none">
        <span class="js-oxPayolutionError_notEmpty">[{ oxmultilang ident="EXCEPTION_PAYOLUTION_INPUT_NOTALLFIELDS" }]</span>
        <span class="js-oxPayolutionError_phoneFormat">[{ oxmultilang ident="EXCEPTION_PAYOLUTION_INPUT_INVALID_PHONE_FORMAT" }]</span>
    </p>
        <br/>
        <br/>
    [{elseif $element->type() == 'birthday' }]
        <label>[{ oxmultilang ident=$element->title() }]</label>
        <span style="float: left">
            <select class="js-oxPayolutionValidate js-oxPayolutionValidate_birthday oxPayolutionDay [{if !$element->required()}]js-oxPayolutionValidate_allowEmpty[{/if}]">
                <option value="">-</option>

                    [{section name=day start=1 loop=32 step=1}]
                    <option value="[{$smarty.section.day.index}]">[{$smarty.section.day.index|string_format:"%02d"}]</option>
                    [{/section}]
                </select>
                /
                <select class="js-oxPayolutionValidate js-oxPayolutionValidate_birthday oxPayolutionMonth  [{if !$element->required()}]js-oxPayolutionValidate_allowEmpty[{/if}]">
                    <option value="">-</option>
                    [{section name=mnth start=1 loop=13 step=1}]
                    <option value="[{$smarty.section.mnth.index}]">[{$smarty.section.mnth.index|string_format:"%02d"}]</option>
                    [{/section}]
                </select>
                /
                [{assign var=iCurYear value=$smarty.now|date_format:"%Y"}]
                <select class="js-oxPayolutionValidate js-oxPayolutionValidate_birthday oxPayolutionYear  [{if !$element->required()}]js-oxPayolutionValidate_allowEmpty[{/if}]">
                    <option value="">-</option>
                    [{section name=yr loop=$iCurYear+1 max=100 step=-1}]
                    <option value="[{$smarty.section.yr.index}]">[{$smarty.section.yr.index}]</option>
                    [{/section}]
                </select>
                <input type="hidden" class="birthday-value" name="[{$element->name()}]" value="[{$element->value()}]"/>
            </span>
            [{if $element->help()}]
                <input type="button" class="payo-help"/>
                <div class="payo-help-text">[{$oViewConf->translateWithArgs($element->help(), $element->helpArgs())}]</div>
                <div style="clear: both"></div>
            [{/if}]
            <p class="oxValidateError payolution-oxValidateError-limit-width" style="display: none;">
                <span class="js-oxPayolutionError_notEmpty">[{ oxmultilang ident="EXCEPTION_PAYOLUTION_INPUT_NOTALLFIELDS" }]</span>
                <span class="js-oxPayolutionError_birthdayAllFields">[{ oxmultilang ident="EXCEPTION_PAYOLUTION_INPUT_NO_BIRTHDAY_FIELD" }]</span>
                <span class="js-oxPayolutionError_birthdayIncorect">[{ oxmultilang ident="PAYOLUTION_ERROR_MESSAGE_INCORRECT_BIRTHDAY_DATE" }]</span>
            </p>
        <br/>
        [{/if}]
</li>
[{/if}]