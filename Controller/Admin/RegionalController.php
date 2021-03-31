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
namespace TopConcepts\Payolution\Module\Controller\Admin;

use OxidEsales\Eshop\Application\Controller\Admin\ShopConfiguration;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\Request;

/**
 * Class RegionalController
 * @package TopConcepts\Payolution\Module\Controllers\Admin
 */
class RegionalController extends ShopConfiguration
{
    /**
     * Current class template name.
     * @var string
     */
    protected $_sThisTemplate = 'payolution_regional_settings.tpl';

    /**
     * Rendering Admin start page
     *
     * @return string
     */
    public function render()
    {
        // force shopid as parameter
        // Pass shop OXID so that shop object could be loaded
        $sShopOXID = $this->getConfig()->getShopId();
        $this->setEditObjectId($sShopOXID);

        parent::render();

        return $this->_sThisTemplate;
    }

    /**
     * Get currency abbreviation by country iso2
     *
     * @param string $sCountryISO2
     *
     * @return string
     */
    public function getCurrencyAbbr($sCountryISO2)
    {
        $aCountry2Currency = Registry::getConfig()->getConfigParam('aPayolutionCountry2Currency');

        return isset($aCountry2Currency[$sCountryISO2]) ? $aCountry2Currency[$sCountryISO2] : '';
    }

    /**
     * Saves shop configuration variables
     *
     * @return void
     */
    public function saveConfVars()
    {
        $myConfig = $this->getConfig();
        $sShopId = $this->getEditObjectId();
        $sModule = $this->_getModuleForConfigVars();
        if(empty($sModule)) {
            $sModule = 'module:payolution';
        }
        $request = Registry::get(Request::class);
        foreach ($this->_aConfParams as $sType => $sParam) {
            $aConfVars = $request->getRequestParameter($sParam);
            if (is_array($aConfVars)) {
                foreach ($aConfVars as $sName => $sValue) {
                    /**
                     * Only for payolution_regional settings, task number: 0000003
                     * Replacing comma with a dot
                     */
                    $sValue = str_replace(',', '.', $sValue);
                    $oldValue = $myConfig->getConfigParam($sName);
                    if ($sValue !== $oldValue) {
                        $myConfig->saveShopConfVar(
                            $sType,
                            $sName,
                            $this->_serializeConfVar($sType, $sName, $sValue),
                            $sShopId,
                            $sModule
                        );
                    }
                }
            }
        }
    }
}
