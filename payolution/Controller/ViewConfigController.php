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
namespace TopConcepts\Payolution\Module\Controller;

use OxidEsales\Eshop\Application\Model\Country;
use OxidEsales\Eshop\Application\Model\CountryList;
use OxidEsales\Eshop\Application\Model\Payment;
use OxidEsales\Eshop\Core\Price;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\UtilsObject;
use OxidEsales\Eshop\Core\ViewConfig;
use TopConcepts\Payolution\AccessPoint;
use TopConcepts\Payolution\Module\Core\Exception\PayolutionException;
use TopConcepts\Payolution\PayolutionModule;
use TopConcepts\Payolution\Utils\JavascriptLibraryUtils;

/**
 * Extends oxViewConfig to enable access of additional data
 * Class ViewConfigController
 * @see ViewConfig
 * @package TopConcepts\Payolution\Module\Controllers
 */
class ViewConfigController extends ViewConfigController_Parent
{
    /**
     * Payolution country list
     *
     * @var CountryList
     */
    protected $_oCountryList = null;

    /**
     * Documentation URL
     *
     * @var string
     */
    protected $_sDownloadLinkBase = 'http://www.topconcepts.de/files/downloads/';

    /**
     * Return Payolution countries list
     *
     * @return CountryList
     */
    public function getPayolutionCountries()
    {
        if ($this->_oCountryList === null) {
            $oCountries = oxNew(CountryList::class);
            $oCountries->getPayolutionCountries();

            $this->_oCountryList = $oCountries;
        }

        return $this->_oCountryList;
    }

    /**
     * Get Payolution module url with added path
     *
     * @param string $sPath
     * @return string
     */
    public function getPayolutionModuleUrl($sPath = '')
    {
        $sUrl = str_replace(
            rtrim($this->getConfig()->getConfigParam('sShopDir'), '/'),
            rtrim($this->getConfig()->getCurrentShopUrl(false), '/'),
            $this->getConfig()->getConfigParam('sShopDir') . 'modules/tc/payolution/' . $sPath
        );

        return $sUrl;
    }

    /**
     * @param string $payment
     *
     * @return string
     */
    public function getPayolutionIcon($payment)
    {
        return $this->getPayolutionModuleUrl('out/img/' . $payment . '.png');
    }

    /**
     * Get Download link
     *
     * @return mixed
     */
    public function getDownloadLink()
    {
        $iLang = Registry::getLang()->getTplLanguage();
        $sLangTag = Registry::getLang()->getLanguageAbbr($iLang);

        /* @var $module PayolutionModule */
        $module = oxNew(AccessPoint::class)->getModule();
        $sVersion = $module->getModuleVersion();

        return $this->_sDownloadLinkBase . "payolution-for-oxid-" . $sLangTag . "-" . $sVersion . ".pdf";
    }

    /**
     * @return PayolutionModule
     */
    public function getPayolutionModule()
    {
        /* @var $accessPoint AccessPoint */
        $accessPoint = oxNew(AccessPoint::class);

        return $accessPoint->getModule();
    }

    /**
     * @return false|string
     */
    public function getPayoJsUpdateTime()
    {
        /** @var JavascriptLibraryUtils $jsLibDownloader */
        $jsLibDownloader = oxNew(JavascriptLibraryUtils::class);

        try {
            $updateTimestamp = $jsLibDownloader->getLastUpdateTime();
        } catch (PayolutionException $e) {

            return '';
        }

        return date('YmdHis', $updateTimestamp);
    }

    /**
     * Get config parameter to check if oxid functionality is turned on or off.
     *
     * @param string $sParamName config parameter name.
     *
     * @return bool
     */
    public function isOxidFunctionalityEnabled($sParamName)
    {
        $config = Registry::getConfig();

        return (bool) $config->getConfigParam($sParamName);
    }

    /**
     * @param string $ident
     * @param array $args
     *
     * @return string
     */
    public function translateWithArgs($ident, $args)
    {
        $lang = Registry::getLang();
        $translation = $lang->translateString($ident);

        if ($args !== false) {
            if (is_array($args)) {
                $translation = vsprintf($translation, $args);
            } else {
                $translation = sprintf($translation, $args);
            }
        }

        return $translation;
    }

    /**
     * @return bool
     */
    public function isActiveThemeFlow()
    {
        return method_exists($this, 'getActiveTheme')
               ? $this->getActiveTheme() == 'flow'
               : false;
    }
    /**
     * @return string
     */
    public function getActiveCountryIso()
    {
        /** @var Country $country */
        $country = oxNew(Country::class);
        $user = Registry::getSession()->getUser();
        if ($user) {
            $country->load($user->oxuser__oxcountryid->value);
            if ($country->isLoaded()) {

                return $country->oxcountry__oxisoalpha2->value;
            }
        }

        return '';
    }

    /**
     * @return bool
     */
    public function needToDisplayFraudScript()
    {
        return in_array(parent::getActionClassName(), ['payment', 'order']);
    }

    /**
     * @return string
     */
    public function getFraudSessionId()
    {
        $session = Registry::getSession();

        if ($session->hasVariable('FraudSessionId')) {
            $key = $session->getVariable('FraudSessionId');
        } else {
            $hosts = explode('.', parse_url(Registry::getConfig()->getShopUrl(),PHP_URL_HOST));
            $key = $hosts[count($hosts) - 2] . '_' . UtilsObject::getInstance()->generateUId();

            $session->setVariable('FraudSessionId', $key);
        }

        return $key;
    }

    /**
     * @return string
     */
    public function getFraudPreventionOrgId()
    {
        return '363t8kgq';
    }

    /**
     * @param Payment $paymentMethod
     *
     * @return Price|null
     */
    public function getPaymentPrice($paymentMethod)
    {
        $price = null;
        if ($paymentMethod instanceof Payment) {
            $price = $paymentMethod->getPrice();
        }

        if (!$price instanceof Price || $price->getPrice() <= 0) {
            $price = null;
        }

        return $price;
    }

    /**
     * @param string $name
     *
     * @return object|null
     */
    public function getCurrencyFromName($name)
    {
        $currencies = Registry::getConfig()->getCurrencyArray();
        if (count($currencies)) {
            foreach ($currencies as $currency) {
                if ($currency->name == $name) {
                    return $currency;
                }
            }
        }

        return null;
    }

    /**
     * @return object {id, name, rate, dec, thousand, sign, decimal, side, selected}
     */
    public function getSessionCurrency()
    {
        $currency = Registry::getConfig()->getActShopCurrencyObject();
        if(!preg_match('//u', $currency->sign)) {
            $currency->sign = iconv('ISO 8859-15', 'UTF-8', $currency->sign);
        }
        return $currency;
    }

}
