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
namespace TopConcepts\Payolution\Module\Model;

use OxidEsales\Eshop\Application\Model\CountryList;
use OxidEsales\Eshop\Core\Registry;

/**
 * Class Payolution_oxCountryList extends default OXID oxCountryList
 * class to add specific logic needed for Payolution payment module
 *
 * Class CountryListModel
 * @see CountryList
 * @package TopConcepts\Payolution\Module\Model
 */
class CountryListModel extends CountryListModel_Parent
{
    /**
     * Generate Payolution country list
     *
     * @return void
     */
    public function getPayolutionCountries()
    {
        $aCountries2PayolutionId = Registry::getConfig()->getConfigParam('aPayolutionCountries');
        $aCountries = array_flip($aCountries2PayolutionId);

        $sViewName = getViewName('oxcountry', Registry::getLang()->getObjectTplLanguage() );
        $sSelect = "SELECT oxid, oxtitle, oxisoalpha2 FROM {$sViewName} WHERE oxisoalpha2 IN ('" . implode("', '", $aCountries) . "')";
        $this->selectString($sSelect);
    }
}
