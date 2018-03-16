<?php
/**
 * Copyright 2015 Payolution GmbH
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
namespace Payolution\Form;
use OxidEsales\Eshop\Core\Registry;
use Payolution\Order\OrderContext;

/**
 * Class Payolution_Form_B2c
 */
class B2CForm extends BaseFormAbstract
{
    /**
     * @var ElementForm
     */
    private $_birthday;
    /**
     * @var ElementForm
     */
    private $_termsAndConditions;

    /**
     * @param OrderContext $context
     */
    protected function bindContext(OrderContext $context)
    {
        /* @var $helper BindHelper */
        $helper = oxNew(BindHelper::class);
        $this->get('dynvalue[payolution_b2c_birthday]')->setValue($helper->getBirthday($context->user()));
    }

    /**
     * Add elements
     */
    protected function init()
    {
        $this->_birthday = $this->add(ElementForm::create(
            ElementForm::BIRTHDAY,
            'birthday',
            'dynvalue[payolution_b2c_birthday]',
            null,
            'PAYOLUTION_HELP_BIRTHDAY'
        )->addHelpArg(Registry::getLang()->translateString('PAYOLUTION_HELP_PAYMENT_B2C'))
        );

        $this->_privacyPolicy = $this->add(ElementForm::create(
            ElementForm::CHECKBOX,
            'privacy_policy_b2c',
            'dynvalue[payolution_b2c_privacy]', null, null, true,
            Registry::getLang()->translateString('PAYOLUTION_ERROR_DATA_PRIVACY'))
        );
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
    public function termsAndConditions()
    {
        return $this->_termsAndConditions;
    }
}
