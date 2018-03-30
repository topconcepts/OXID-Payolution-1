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
use OxidEsales\Eshop\Application\Model\Country;
use OxidEsales\Eshop\Core\DatabaseProvider as Db;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\Request;
use OxidEsales\Eshop\Core\UtilsView;

/**
 * Class Payolution_Config for module configuration in OXID backend
 *
 * Class ConfigController
 * @package TopConcepts\Payolution\Module\Controller\Admin
 */
class ConfigController extends ShopConfiguration
{
    /**
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = 'payolution_config.tpl';

    /**
     * Request parameter container
     *
     * @var array
     */
    protected $_aParameters = [];

    /**
     * Validating only this fields (image url)
     *
     * @var array
     */
    protected $_aUrlParams = [
        'sPayolutionCheckoutCdnUrl' => 'PAYOLUTION_CHECKOUT_CDN_URL',
        'sOtherPaymentCdnUrl' => 'OTHER_PAYMENT_CDN_URL',
        'sExpressCheckoutButtonCdnUrl' => 'EXPRESS_CHECKOUT_BUTTON_CDN_URL',
    ];


    /**
     * Sets parameter
     *
     * @param $sName
     * @param $sValue
     *
     * @return string
     */
    public function setParameter($sName, $sValue)
    {
        $this->_aParameters[$sName] = $sValue;
    }

    /**
     * Return parameter from container
     *
     * @param $sName
     * @return string
     */
    public function getParameter($sName)
    {
        return $this->_aParameters[$sName];
    }

    /**
     * Render logic
     *
     * @see Admin/oxAdminDetails::render()
     * @return string
     */
    public function render()
    {
        $db = Db::getDb(Db::FETCH_MODE_ASSOC);
        $sql = 'SELECT oxid FROM oxconfig WHERE oxvarname = \'aPayolutionLanguage\' AND oxshopid = ?';
        $isPayolutionLanguage = $db->getOne($sql, [Registry::getConfig()->getShopId()]);

        if (!$isPayolutionLanguage) {
            Registry::get(UtilsView::class)->addErrorToDisplay(
                Registry::getLang()->translateString('PAYOLUTION_INSTALL_NOT_INSTALLED')
            );

            return 'payolution_empty.tpl';
        }

        // force shopid as parameter
        // Pass shop OXID so that shop object could be loaded
        $sShopOXID = $this->getConfig()->getShopId();
        $this->setEditObjectId($sShopOXID);


        $country       = oxNew(Country::class);
        $countryTable  = $country->getViewName();
        $activeSnippet = $country->getSqlActiveSnippet();

        $config = Registry::getConfig();
        $this->_aViewData['bPayolutionShowPriceOnBasket'] = $config->getConfigParam('bPayolutionShowPriceOnBasket');
        $this->_aViewData['bPayolutionShowPriceOnCategory'] = $config->getConfigParam('bPayolutionShowPriceOnCategory');
        $this->_aViewData['bPayolutionShowPriceOnDetails'] = $config->getConfigParam('bPayolutionShowPriceOnDetails');
        $this->_aViewData['bPayolutionShowPriceOnHomePage'] = $config->getConfigParam('bPayolutionShowPriceOnHomePage');

        $this->_aViewData['activeCountries'] = $db
            ->getAll('SELECT `OXISOALPHA2`, `OXTITLE` FROM '.$countryTable.' WHERE '.$activeSnippet);

        parent::render();

        return $this->_sThisTemplate;
    }

    /**
     * Save configuration values
     *
     * @return void
     */
    public function save()
    {
        $this->checkConfParams();
        // Save parameters to container
        $this->fillContainer();
        $this->doSave();
    }

    /**
     * Filter Payolution values
     *
     * @return void
     */
    protected function _filterPayolutionConfig()
    {
        // do some value filtering
        $aCountriesMap = Registry::getConfig()->getConfigParam('aPayolutionCountries');
        foreach ($aCountriesMap as $sCountryISO2 => $iPayolutionCountryId) {
            $confstrs = $this->getParameter('confstrs');
            if ($dFee = $confstrs['iPayolutionInvoiceFee' . $sCountryISO2]) {
                $dFee = str_replace(',', '.', $dFee);
                $confstrs['iPayolutionInvoiceFee' . $sCountryISO2] = $dFee;
                $this->setParameter('confstrs', $confstrs);
            }
            if ($dAmount = $confstrs['iPayolutionMonthlyRateMinAmount' . $sCountryISO2]) {
                $dAmount = str_replace(',', '.', $dAmount);
                $confstrs['iPayolutionMonthlyRateMinAmount' . $sCountryISO2] = $dAmount;
                $this->setParameter('confstrs', $confstrs);
            }
        }
    }

    /**
     * Fill parameter container with request values
     */
    private function fillContainer()
    {
        $request = Registry::get(Request::class);

        foreach ($this->_aConfParams as $sType => $sParam) {
            $this->setParameter($sParam, $request->getRequestParameter($sParam));
        }
    }

    /**
     * Save vars as shop config does
     */
    private function doSave()
    {
        $this->performConfVarsSave();
        $sOxid = $this->getEditObjectId();
        //saving additional fields ("oxshops__oxdefcat"") that goes directly to shop (not config)
        $oShop = oxNew("oxshop");
        if ($oShop->load($sOxid)) {
            $oShop->assign(Registry::get(Request::class)->getRequestParameter("editval"));
            $oShop->save();
        }
    }

    /**
     * Shop config variable saving
     */
    private function performConfVarsSave()
    {
        $this->resetContentCache();

        $aKeys = array_keys($this->_aUrlParams);
        foreach ($this->_aConfParams as $sType => $sParam) {
            $aConfVars = $this->getParameter($sParam);
            if (!is_array($aConfVars)) {
                continue;
            }

            $this->_performConfVarsSave($sType, $aConfVars, $aKeys);
        }
    }

    /**
     * Save config parameter
     *
     * @param $sConfigType
     * @param $aConfVars
     * @param $aKeys
     */
    protected function _performConfVarsSave($sConfigType, $aConfVars, $aKeys)
    {
        $myConfig = $this->getConfig();
        $sShopId = $this->getEditObjectId();
        $sModule = $this->_getModuleForConfigVars();

        foreach ($aConfVars as $sName => $sValue) {
            if (in_array($sName, $aKeys)) {
                $sCheckValue = $this->replaceLocationString($sValue);
                if (!$this->checkImageHttpStatus($sCheckValue, $sName)) {
                    unset($sValue);
                }

                // some config values depend on language
                $sName = $sName . '_' . Registry::get(Request::class)->getRequestParameter('changelang');
            }

            $oldValue = $myConfig->getConfigParam($sName);
            if ($sValue !== $oldValue) {
                $myConfig->saveShopConfVar(
                    $sConfigType,
                    $sName,
                    $this->_serializeConfVar($sConfigType, $sName, $sValue),
                    $sShopId,
                    $sModule
                );
            }
        }
    }

    /**
     * Replacing parameter place holders with current params
     *
     * @param $sValue
     * @return mixed
     */
    public function replaceLocationString($sValue)
    {
        $sDefault = Registry::getConfig()->getConfigParam('sPayolutionDefaultCountry');

        return str_replace('{$country}', $sDefault, $sValue);
    }

    /**
     * Checking image response header code
     *
     * @param $sUrl
     * @param $sName
     * @return bool
     */
    public function checkImageHttpStatus($sUrl, $sName)
    {
        if ($sUrl) {
            $imageSize = getimagesize($sUrl);
            if ($imageSize) {
                return true;
            }

            Registry::get(UtilsView::class)->addErrorToDisplay(
                Registry::getLang()->translateString('BAD_IMAGE_LOCATION') . ' "' . Registry::getLang(
                )->translateString($this->_aUrlParams[$sName]) . '"'
            );

            return false;
        }
    }

    /**
     * Add params if not yet set (compat. version)
     */
    private function checkConfParams()
    {
        if (!isset($this->_aConfParams)) {
            $this->_aConfParams = [
                "bool" => "confbools",
                "str" => "confstrs",
                "arr" => "confarrs",
                "aarr" => "confaarrs"
            ];
        }
    }
}
