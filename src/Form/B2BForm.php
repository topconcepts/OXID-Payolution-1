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
namespace TopConcepts\Payolution\Form;

use OxidEsales\Eshop\Core\Registry;
use TopConcepts\Payolution\Client\Type\Customer\CompanyTypes;
use TopConcepts\Payolution\Order\OrderContext;

/**
 * Class B2BForm
 * @package TopConcepts\Payolution\Form
 */
class B2BForm extends BaseFormAbstract
{
    /**
     * @var ElementForm
     */
    private $_ustId;

    /**
     * @var ElementForm
     */
    private $_name;

    /**
     * @var ElementSelectForm
     */
    private $_type;

    /**
     * @var ElementForm
     */
    private $_ownerGiven;

    /**
     * @var ElementForm
     */
    private $_ownerFamily;

    /**
     * @var ElementForm
     */
    private $_ownerBirthdate;

    /**
     * @param OrderContext $context
     */
    protected function bindContext(OrderContext $context)
    {
        /* @var $helper BindHelper */
        $helper = oxNew(BindHelper::class);
        $user = Registry::getSession()->getUser();
        
        $this->get('dynvalue[payolution_b2b_ust_id]')
          ->setValue($helper->getUstId(Registry::getSession()->getUser()));
        $this->get('dynvalue[payolution_b2b_type]')
          ->setValue($helper->getCompanyTypeSelect());
        $this->get('dynvalue[payolution_b2b_name]')
          ->setValue($user->oxuser__oxcompany->value);
        $this->get('dynvalue[payolution_b2b_owner_family]')
          ->setValue('');
        $this->get('dynvalue[payolution_b2b_owner_given]')
          ->setValue('');
        $this->get('dynvalue[payolution_b2b_owner_birthday]')
          ->setValue('');
    }

    /**
     * Add elements
     */
    protected function init()
    {
        $helper = oxNew(BindHelper::class);

        $this->_type = $this->add(
            ElementSelectForm::create(
                ElementSelectForm::TYPE,
                'type',
                'dynvalue[payolution_b2b_type]'
            )->setRequired(true)->setDataValues(
                array(
                    'typeMap' => CompanyTypes::getTypesMap(),
                    'companyNames' => CompanyTypes::getCompanyNameTranslations(),
                )
            )
        );

        $this->_name = $this->add(ElementForm::create(
          ElementForm::INPUT,
          'name',
          'dynvalue[payolution_b2b_name]')->setRequired(true)
        );

        $this->_ownerFamily = $this->add(ElementForm::create(
          ElementForm::INPUT,
          'owner_family',
          'dynvalue[payolution_b2b_owner_family]')->setRequired(true)
        );

        $this->_ownerGiven = $this->add(ElementForm::create(
          ElementForm::INPUT,
          'owner_given',
          'dynvalue[payolution_b2b_owner_given]')->setRequired(true)
        );

        $this->_ustId = $this->add(ElementForm::create(
            $helper->getUstId(Registry::getSession()->getUser()) ?
                ElementForm::HIDDEN : ElementForm::INPUT,
            'ust_id',
            'dynvalue[payolution_b2b_ust_id]')->setRequired(true)
        );

        $this->_ownerBirthdate = $this->add(ElementForm::create(
          ElementForm::BIRTHDAY,
          'owner_birthday',
          'dynvalue[payolution_b2b_owner_birthday]',
          null,
          'PAYOLUTION_HELP_BIRTHDAY_B2B',
          false
        )->addHelpArg(Registry::getLang()->translateString('PAYOLUTION_HELP_PAYMENT_B2B'))
        );

        $this->_privacyPolicy = $this->add(ElementForm::create(
          ElementForm::CHECKBOX,
          'privacy_policy_b2b',
          'dynvalue[payolution_b2b_privacy]', null, null, true,
          Registry::getLang()->translateString('PAYOLUTION_ERROR_DATA_PRIVACY'))
        );
    }

    /**
     * @return ElementForm
     */
    public function ustId()
    {
        return $this->_ustId;
    }

    /**
     * @return ElementForm
     */
    public function birthday()
    {
        return $this->_ownerBirthdate;
    }

    /**
     * @return ElementForm
     */
    public function ownerBirthdate()
    {
        return $this->_ownerBirthdate;
    }

    /**
     * @return ElementForm
     */
    public function ownerFamily()
    {
        return $this->_ownerFamily;
    }

    /**
     * @return ElementForm
     */
    public function ownerGiven()
    {
        return $this->_ownerGiven;
    }

    /**
     * @return ElementForm
     */
    public function type()
    {
        return $this->_type;
    }

    /**
     * @return ElementForm
     */
    public function name()
    {
        return $this->_name ;
    }
}
