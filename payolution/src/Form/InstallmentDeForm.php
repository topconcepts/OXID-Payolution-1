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

/**
 * Class InstallmentDeForm
 * @package TopConcepts\Payolution\Form
 */
class InstallmentDeForm extends InstallmentForm
{
    /**
     * @var ElementForm
     */
    private $_iban;

    /**
     * @var ElementForm
     */
    private $_accountHolder;

    /**
     * Add elements
     */
    protected function init()
    {
        parent::init();

        $this->add(ElementForm::create(
          ElementForm::BUTTON,
          'installment_availability',
          'installment_availability')
        );

        $this->_iban = $this->add(ElementForm::create(
          ElementForm::INPUT,
          'iban',
          'dynvalue[payolution_installment_iban]', null, 'PAYOLUTION_HELP_IBAN')
          ->setClassName('field-iban')
        );

        $this->_accountHolder = $this->add(ElementForm::create(
          ElementForm::INPUT,
          'account_holder',
          'dynvalue[payolution_installment_account_holder]')
          ->setClassName('field-account_holder')
        );
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
    public function accountHolder()
    {
        return $this->_accountHolder;
    }
}
