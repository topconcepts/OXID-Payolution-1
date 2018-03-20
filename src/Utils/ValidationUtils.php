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
namespace TopConcepts\Payolution\Utils;

use OxidEsales\Eshop\Application\Model\Address;
use TopConcepts\Payolution\Form\B2CForm;
use TopConcepts\Payolution\Form\InstallmentForm;

/**
 * Class ValidationUtils
 * @package TopConcepts\Payolution\Utils
 */
class ValidationUtils
{
    /**
     * oxAddress fields that should be the same when validating shipping and
     * billing address match
     *
     * @var array
     */
    private $addressFields = [
      'oxcompany',
      'oxfname',
      'oxlname',
      'oxstreet',
      'oxstreetnr',
      'oxaddinfo',
      'oxcity',
      'oxcountryid',
      'oxstateid',
      'oxzip',
    ];

    /**
     * Check that all mandatory fields for Payolution B2C payment method are
     * not empty
     *
     * @param B2CForm $form
     *
     * @return bool
     */
    public function mandatoryFieldsB2c(B2CForm $form)
    {
        return
          trim($form->birthday()->value()) ? true : false; // Birth date required for age checking
    }

    /**
     * Check that all mandatory fields for Payolution Installment payment
     * method are not empty
     *
     * @param InstallmentForm $user
     *
     * @return bool
     */
    public function mandatoryFieldsInstallment(InstallmentForm $user)
    {
        // TODO implement
        return true;
    }

    /**
     * Check if shipping and billing addresses match
     *
     * @param Address $shipAddress
     * @param Address $bilAddress
     *
     * @return bool
     */
    public function addrsMatch($shipAddress, $bilAddress)
    {
        // There must be at least one comparable field
        if (empty($this->addressFields)) {
            return false;
        }

        foreach ($this->addressFields as $field) {
            if (trim($shipAddress->{'oxaddress__' . $field}) != trim($bilAddress->{'oxaddress__' . $field})) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if user age is above or equal to 18
     *
     * @param string $birthday yyyy-mm-dd format
     *
     * @return bool
     */
    public function ageRestriction($birthday)
    {
        if (!trim($birthday)) {
            return false;
        }

        $date = new \DateTime($birthday);
        return (int) $date->diff(new \DateTime(), true)->format('%y') >= 18;
    }
}
