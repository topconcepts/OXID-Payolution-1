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
namespace TopConcepts\Payolution\Module\Model;

use OxidEsales\Eshop\Application\Model\Country;
use OxidEsales\Eshop\Core\Registry;

/**
 * Class Payolution_oxCountry extends default OXID oxCountry class
 * to provide logic needed by Payolution payment module
 *
 * Class CountryModel
 * @see Country
 * @package TopConcepts\Payolution\Module\Model
 */
class CountryModel extends CountryModel_Parent
{
    /**
     * Get respective Payolution country id
     * 
     * @return bool|string
     */
    public function getPayolutionId()
    {
        $aCountries2PayolutionId = Registry::getConfig()->getConfigParam('aPayolutionCountries');
        $sCountryISO2 = $this->oxcountry__oxisoalpha2->value;
        
        if (is_array($aCountries2PayolutionId) && isset($aCountries2PayolutionId[$sCountryISO2])) {
            return $aCountries2PayolutionId[$sCountryISO2];
        }
        
        return false;
    }
}
