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
namespace TopConcepts\Payolution\Client\Type\Customer;

use OxidEsales\Eshop\Core\Registry;

/**
 * Class CompanyTypes
 * @package TopConcepts\Payolution\Client\Type\Customer
 */
class CompanyTypes
{
    const TRANSLATION_PREFIX = 'PAYOLUTION_COMPANY_TYPE_';
    const TYPE_PUBLIC = 'PUBLIC';
    const TYPE_REGISTERED = 'REGISTERED';
    const TYPE_SOLE = 'SOLE';
    const TYPE_COMPANY = 'COMPANY';
    const TYPE_OTHER = 'OTHER';

    /**
     * @return array
     */
    public static function getTypes() {
        return [
            self::TYPE_COMPANY,
            self::TYPE_PUBLIC,
            self::TYPE_REGISTERED,
            self::TYPE_SOLE,
            self::TYPE_OTHER,
            
        ];
    }

    /**
     * @return array
     */
    public static function getTypesMap()
    {
        $base = [
            'type', 'name', 'ust_id', 'privacy', 'phone'
        ];
        
        return [
            self::TYPE_PUBLIC => $base,
            self::TYPE_REGISTERED => $base,
            self::TYPE_COMPANY => $base,
            self::TYPE_SOLE => ['owner_family', 'owner_given', 'owner_birthday', 'type', 'name', 'privacy', 'phone'],
            self::TYPE_COMPANY => $base,
            self::TYPE_OTHER => $base,
        ];
    }

    /**
     * @return array
     */
    public static function getCompanyNameTranslations()
    {
        $base = Registry::getLang()->translateString('PAYOLUTION_COMPANY_NAME_BASE');

        return [
            self::TYPE_PUBLIC => Registry::getLang()->translateString('PAYOLUTION_COMPANY_NAME_PUBLIC'),
            self::TYPE_REGISTERED => Registry::getLang()->translateString('PAYOLUTION_COMPANY_NAME_REGISTERED'),
            self::TYPE_COMPANY => $base,
            self::TYPE_SOLE => $base,
            self::TYPE_COMPANY => $base,
            self::TYPE_OTHER => $base
        ];
    }
    
}
