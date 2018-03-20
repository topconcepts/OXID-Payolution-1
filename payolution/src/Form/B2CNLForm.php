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

use TopConcepts\Payolution\Order\OrderContext;

/**
 * Class B2CNLForm
 * @package TopConcepts\Payolution\Form
 */
class B2CNLForm extends B2CForm
{
    /**
     * @var ElementForm
     */
    private $_phone;

    /**
     * @return ElementForm
     */
    public function phone()
    {
        return $this->_phone;
    }

    /**
     * @param OrderContext $context
     */
    protected function bindContext(OrderContext $context)
    {
        parent::bindContext($context);

        /* @var $helper BindHelper */
        $helper = oxNew(BindHelper::class);
        $this->get('dynvalue[payolution_b2c_phone]')->setValue($helper->getPhone($context->user()));
    }

    /**
     * Add elements
     */
    protected function init()
    {
        $this->_phone = $this->add(ElementForm::create(
            $this->getPhoneElementType(),
            'phone',
            'dynvalue[payolution_b2c_phone]')
        );

        parent::init();
    }

    /**
     * Get form phone element type
     *
     * @return string
     */
    public function getPhoneElementType()
    {
        $context = $this->orderingContext();

        return ($context->isUserPhoneSet() && !$context->isPaymentError()) ?
            ElementForm::HIDDEN :
            ElementForm::PHONE;
    }
}
