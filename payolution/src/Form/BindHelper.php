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

use OxidEsales\Eshop\Application\Model\User;
use OxidEsales\Eshop\Core\Registry;
use TopConcepts\Payolution\Client\Type\Customer\CompanyTypes;

/**
 * Class BindHelper
 * @package TopConcepts\Payolution\Form
 */
class BindHelper
{
    /**
     * @return bool
     */
    private function isActiveThemeFlow()
    {
        $activeView = Registry::getConfig()->getActiveView();

        return $activeView ? $activeView->getViewConfig()->isActiveThemeFlow() : false;
    }

    /**
     * @param User $user
     *
     * @return array
     */
    public function getBirthday($user)
    {
        $hasValue = $user->oxuser__oxbirthdate->value && $user->oxuser__oxbirthdate->value != '0000-00-00';
        $format = '%Y-%m-%d';
        if ($this->isActiveThemeFlow()) {
            $format = '%d.%m.%Y';
        }

        return $hasValue ? strftime($format, strtotime($user->oxuser__oxbirthdate->value)) : '';
    }

    /**
     * @param $value
     * @return string
     */
    public function formatBirthday($value)
    {
        $format = '%Y-%m-%d';
        if ($this->isActiveThemeFlow()) {
            $format = '%d.%m.%Y';
        }

        return  strftime($format,strtotime($value));
    }
    /**
     * @param User $user
     *
     * @return string
     */
    public function getPhone($user)
    {
        return preg_replace('/[\s\-\/\(\)]+/', '', $user->oxuser__oxfon->value);
    }

    /**
     * @param User $user
     *
     * @return string
     */
    public function getUstId($user)
    {
        // TODO: must implement company registration number retrieval, now only `oxustid` (user VAT number) is impl.
        return $user->oxuser__oxustid->value ? $user->oxuser__oxustid->value : '';
    }

    /**
     * @param User $user
     * @return string
     */
    public function getHolderName($user)
    {
        return $user->oxuser__oxfname->value.' '.$user->oxuser__oxlname->value;
    }

    /**
     * @param User $user
     * @return string
     */
    public function getCompanyName($user)
    {
        return $user->oxuser__ocompany->value;
    }

    /**
     * @return SelectedItemForm[]
     */
    public function getCompanyTypeSelect() {
        $types = array();

        foreach (CompanyTypes::getTypes() as $typeKey) {
            $type = oxNew(SelectedItemForm::class);
            $type->setValue($typeKey);
            $type->setTitle(CompanyTypes::TRANSLATION_PREFIX . $typeKey);
            $type->setSelected(false);
            $types[$typeKey] = $type;
        }

        return $types;
    }
}
