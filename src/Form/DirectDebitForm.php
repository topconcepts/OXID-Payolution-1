<?php
/**
 * Copyright 2018 Payolution GmbH
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0 [^]
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
namespace TopConcepts\Payolution\Form;

use OxidEsales\Eshop\Core\Registry;
use TopConcepts\Payolution\Order\OrderContext;

/**
 * Class DirectDebitForm
 * @package TopConcepts\Payolution\Form
 */
class DirectDebitForm extends BaseFormAbstract
{
    /**
     * @var ElementForm
     */
    private $_birthday;

    /**
     * @var ElementForm
     */
    private $_holder;

    /**
     * @var ElementForm
     */
    private $_iban;

    /**
     * @var ElementForm
     */
    private $_mandate;

    /**
     * @param OrderContext $context
     */
    protected function bindContext(OrderContext $context)
    {
        /* @var $helper BindHelper */
        $helper = oxNew(BindHelper::class);
        $this->get('dynvalue[payolution_dd_birthday]')->setValue($helper->getBirthday($context->user()));
        $this->get('dynvalue[payolution_dd_holder]')->setValue($helper->getHolderName($context->user()));
    }

    /**
     * Add elements
     */
    protected function init()
    {
        $this->_birthday = $this->add(ElementForm::create(
            ElementForm::BIRTHDAY,
            'birthday',
            'dynvalue[payolution_dd_birthday]',
            null,
            'PAYOLUTION_HELP_BIRTHDAY'
        )->addHelpArg(Registry::getLang()->translateString('PAYOLUTION_HELP_PAYMENT_DD'))
        );

        $this->_holder = $this->add(ElementForm::create(
            ElementForm::INPUT,
            'holder_dd',
            'dynvalue[payolution_dd_holder]'
        ));

        $this->_iban = $this->add(ElementForm::create(
            ElementForm::INPUT,
            'iban_dd',
            'dynvalue[payolution_dd_iban]'
        )->setClassName('field-iban'));

        $this->_mandate = $this->add(ElementForm::create(
            ElementForm::CHECKBOX,
            'mandate_dd',
            'dynvalue[payolution_dd_mandate]'));

        $this->_privacyPolicy = $this->add(ElementForm::create(
            ElementForm::CHECKBOX,
            'privacy_policy_dd',
            'dynvalue[payolution_dd_privacy]', null, null, true,
            Registry::getLang()->translateString('PAYOLUTION_ERROR_DATA_PRIVACY')));
    }

    /**
     * @return ElementForm
     */
    public function birthday()
    {
        return $this->_birthday;
    }

    /**
     * @return ElementForm
     */
    public function holder()
    {
        return $this->_holder;
    }

    /**
     * @return ElementForm
     */
    public function iban()
    {
        return $this->_iban;
    }

    /**
     * @return ElementForm
     */
    public function mandate(){
        return $this->_mandate;
    }
}
