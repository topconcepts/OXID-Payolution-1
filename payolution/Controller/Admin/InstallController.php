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

use OxidEsales\DoctrineMigrationWrapper\Migrations;
use OxidEsales\DoctrineMigrationWrapper\MigrationsBuilder;
use OxidEsales\Eshop\Application\Controller\Admin\ToolsList;
use OxidEsales\Eshop\Application\Model\Payment;
use OxidEsales\Eshop\Core\Exception\StandardException;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\UtilsView;
use OxidEsales\Facts\Config\ConfigFile;
use OxidEsales\Facts\Facts;
use TopConcepts\Payolution\AccessPoint;
use TopConcepts\Payolution\PayolutionModule;
use OxidEsales\Eshop\Core\DatabaseProvider as Db;
use TopConcepts\Payolution\Module\Core\SeoEncoder;

/**
 * For module installation (setup) procedures.
 *
 * Class InstallController
 * @package TopConcepts\Payolution\Module\Controller\Admin
 */
class InstallController extends ToolsList
{
    /**
     * Current class template name
     *
     * @var string
     */
    protected $_sThisTemplate = 'payolution_install.tpl';

    /**
     * Columns that should be installed.
     *
     * @var array
     */
    protected $_aInstalledColumns = [
        'oxorder' => [
            'PAYO_COMPANY_REGISTRATION_NO',
            'PAYO_UNIQUE_ID',
            'PAYO_INVOICE_SENT',
        ],
        'oxuser' => [
            'PAYO_COMPANY_REGISTRATION_NO',
        ],
    ];

    /**
     * General template parameters for payolution module
     *
     * @var array
     */
    protected $_payolutionTemplateParams = [];

    /**
     * Payment option fields. Created in method setPaymentOptions();
     *
     * @var string
     */
    private $paymentOptions;

    /**
     * Init method
     *
     * @return string
     */
    public function init()
    {
        $this->setPaymentOptions();
        $this->_initPayolutionInstall();

        return parent::init();
    }

    /**
     * Set payment fields config string
     */
    private function setPaymentOptions()
    {
        $this->paymentOptions = implode('__@@', [
          'payolution_installment_birthday',
          'payolution_installment_privacy',
          'payolution_installment_iban',
          'payolution_installment_account_holder',
          'payolution_installment_period',
          'payolution_b2c_privacy',
          'payolution_b2c_birthday',
          'payolution_b2c_phone',
          'payolution_b2b_ust_id',
          'payolution_b2b_privacy',
          'payolution_b2b_type',
          'payolution_b2b_owner_given',
          'payolution_b2b_owner_family',
          'payolution_b2b_owner_birthday',
          'payolution_b2b_phone',
          'payolution_dd_birthday',
          'payolution_dd_privacy',
          'payolution_dd_holder',
          'payolution_dd_iban',
          'payolution_dd_mandate',
        ]).'__@@';
    }

    /**
     * Init Payolution installation
     *
     * @return void
     */
    protected function _initPayolutionInstall()
    {
        if (!$this->_isInstalled()) {
            try {
                $this->_install();
            } catch (StandardException $oException) {
                Registry::get(UtilsView::class)->addErrorToDisplay($oException->getMessage());
            }

            Registry::get(UtilsView::class)->addErrorToDisplay(
                Registry::getLang()->translateString('PAYOLUTION_SUCCESS_MODULE_INSTALLED')
            );
        } else {
            $this->setTemplateParam('bModuleInstalled', true);

            /* @var $module PayolutionModule */
            $module = oxNew(AccessPoint::class)->getModule();

            $this->setTemplateParam('sPayolutionVersion', $module->getModuleVersion());
        }
    }

    /**
     * Check if module is installed
     *
     * @return boolean
     */
    protected function _isInstalled()
    {
        if (!$this->_checkTablesInstall()) {
            return false;
        }

        if (!$this->_checkColumnsInstall()) {
            return false;
        }

        if (!$this->_checkConfigurationInstall()) {
            return false;
        }

        if (!$this->_checkModulesInstall()) {
            return false;
        }

        if (!$this->_checkSeoUrlInstall()) {
            return false;
        }

        return true;
    }

    /**
     * Check if tables are installed
     *
     * @return boolean
     */
    protected function _checkTablesInstall()
    {
        $db = Db::getDb(Db::FETCH_MODE_ASSOC);
        if (count($db->getAll("SHOW TABLES LIKE 'payo_history'")) == 0) {
            return false;
        }

        if (count($db->getAll("SHOW TABLES LIKE 'payo_ordershipments'")) == 0) {
            return false;
        }

        if (count($db->getAll("SHOW TABLES LIKE 'payo_logs'")) == 0) {
            return false;
        }

        if (count($db->getAll("SHOW TABLES LIKE 'payo_returnamount'")) == 0) {
            return false;
        }

        return true;
    }

    /**
     * Check if all columns are installed.
     *
     * @return bool
     */
    protected function _checkColumnsInstall()
    {
        foreach ($this->getColumnsToBeInstalled() as $sTable => $aColumns) {
            $aTableColumns = $this->_loadTableColumns($sTable);
            foreach ($aColumns as $sColumn) {
                if (!in_array(strtolower($sColumn), $aTableColumns)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Load and return given table column names.
     *
     * @param $sTable
     *
     * @return array
     */
    protected function _loadTableColumns($sTable)
    {
        $db = Db::getDb(Db::FETCH_MODE_ASSOC);

        $aLoadedColumns = [];
        if (count($db->getAll("SHOW TABLES LIKE '{$sTable}'")) == 1) {
            foreach ((array)$db->getAll("SHOW COLUMNS FROM {$sTable}") as $aColumn) {
                $aColumn = array_values((array)$aColumn);
                $sColumn = strtolower($aColumn[0]);
                $aLoadedColumns[$sColumn] = $sColumn;
            }
        }

        return $aLoadedColumns;
    }

    /**
     * Returns columns which should be installed.
     *
     * @return array
     */
    public function getColumnsToBeInstalled()
    {
        return $this->_aInstalledColumns;
    }

    /**
     * Check if configuration are installed
     *
     * @return boolean
     */
    protected function _checkConfigurationInstall()
    {
        $db = Db::getDb(Db::FETCH_MODE_ASSOC);

        // check if all payment methods are present
        if ($db->getOne(
                "SELECT COUNT(oxid) FROM oxpayments WHERE oxid IN ('payolution_invoice_b2c', 'payolution_invoice_b2b', 'payolution_installment', 'payolution_dd')"
            ) != 4)
        {
            return false;
        }

        // check for configuration settings in current shop
        if (!$db->getOne(
            "SELECT oxid FROM oxconfig WHERE oxvarname = 'aPayolutionLanguage' AND oxshopid = ?",
            [Registry::getConfig()->getShopId()]
        ))
        {
            return false;
        }

        return true;
    }

    /**
     * Check if modules are installed
     *
     * @return boolean
     */
    protected function _checkModulesInstall()
    {
        $config = Registry::getConfig();

        // check if modules are set
        $blFound = false;
        foreach ($config->getConfigParam('aModules') as $sClass => $sExtend) {
            if (strstr($sExtend, 'Payolution')) {
                $blFound = true;
                break;
            }
        }
        if (!$blFound) {
            return false;
        }

        return true;
    }

    /**
     * Check if Seo Url's installed
     *
     * @return boolean
     */
    protected function _checkSeoUrlInstall()
    {
        $config = Registry::getConfig();
        $db = Db::getDb(Db::FETCH_MODE_ASSOC);
        $shopId = $config->getShopId();
        // check for payolution seo URLs in current shop
        $payolutionSeoUrlsCount = $db->getOne(
            "SELECT COUNT(*) FROM oxseo WHERE oxstdurl = 'index.php?cl=PayolutionPdfDownload' AND oxshopid = '{$shopId}'"
        );

        if (!empty($payolutionSeoUrlsCount)) {
            return true;
        }

        return false;
    }

    /**
     * Do installation from install.sql
     *
     * @return bool
     */
    protected function _install()
    {
        $config = $this->getConfig();
        $migrations = $this->setUpMigrationsConfig();

        $migrations->execute('migrations:migrate');
        $this->insertPayments();
        $this->insertSeoUrls();
        $this->updateViews();

        /* @var $module PayolutionModule */
        $module = oxNew(AccessPoint::class)->getModule();
        $config->setConfigParam('sPayolutionVersion', $module->getModuleVersion());

        return true;
    }

    /**
     * Insert Payolution payment methods
     *
     * @return void
     */
    protected function insertPayments()
    {
        $aLangs = $this->getLangArray();

        if ($aLangs) {
            $this->_savePayolutionInvoiceB2C($aLangs);
            $this->_savePayolutionInvoiceB2B($aLangs);
            $this->_savePayolutionInstallment($aLangs);
            $this->_savePayolutionDD($aLangs);
        }
    }

    /**
     * @return void
     */
    protected function insertSeoUrls()
    {
        /** @var SeoEncoder $seoEncoder */
        $seoEncoder = oxNew(SeoEncoder::class);
        $seoEncoder->generatePayoPaymentPdfUrl();
    }

    /**
     * Returns shop language array
     *
     * @return array
     */
    protected function getLangArray()
    {
        return Registry::getLang()->getLanguageArray();
    }

    /**
     * Inserting Payolution Invoice payment
     *
     * @param void
     */
    protected function _savePayolutionInvoiceB2C($aLang)
    {
        /** @var $oPayment Payment */
        $oPayment = oxNew(Payment::class);
        $oPayment->load('payolution_invoice_b2c');
        if ($oPayment->isLoaded()) {
            return;
        }

        $oPayment->setEnableMultilang(false);

        $oPayment->setId('payolution_invoice_b2c');
        $oPayment->oxpayments__oxactive = new Field(1, Field::T_RAW);
        $oPayment->oxpayments__oxaddsum = new Field(0, Field::T_RAW);
        $oPayment->oxpayments__oxaddsumtype = new Field('abs', Field::T_RAW);
        $oPayment->oxpayments__oxaddsumrules = new Field('31', Field::T_RAW);
        $oPayment->oxpayments__oxfromboni = new Field('0', Field::T_RAW);
        $oPayment->oxpayments__oxfromamount = new Field('0', Field::T_RAW);
        $oPayment->oxpayments__oxtoamount = new Field('1000000', Field::T_RAW);
        $oPayment->oxpayments__oxchecked = new Field(0, Field::T_RAW);
        $oPayment->oxpayments__oxsort = new Field('-200', Field::T_RAW);
        $oPayment->oxpayments__oxtspaymentid = new Field('', Field::T_RAW);

        // set multi language fields
        foreach ($aLang as $oLang) {
            $iLanguageId = $oLang->id;
            $sPaymentTitle = Registry::getLang()->translateString('PAYOLUTION_INSTALL_PAYMENT_INVOICE_B2C', $iLanguageId);
            $sTag = Registry::getLang()->getLanguageTag($iLanguageId);
            $oPayment->{'oxpayments__oxdesc' . $sTag} = new Field($sPaymentTitle, Field::T_RAW);
            $oPayment->{'oxpayments__oxvaldesc' . $sTag} = new Field($this->paymentOptions, Field::T_RAW);
        }

        $oPayment->save();
    }

    /**
     * Inserting Payolution Invoice payment
     *
     * @param void
     */
    protected function _savePayolutionInvoiceB2B($aLang)
    {
        /** @var $oPayment Payment */
        $oPayment = oxNew(Payment::class);
        $oPayment->load('payolution_invoice_b2b');
        if ($oPayment->isLoaded()) {
            return;
        }

        $oPayment->setEnableMultilang(false);

        $oPayment->setId('payolution_invoice_b2b');
        $oPayment->oxpayments__oxactive = new Field(1, Field::T_RAW);
        $oPayment->oxpayments__oxaddsum = new Field(0, Field::T_RAW);
        $oPayment->oxpayments__oxaddsumtype = new Field('abs', Field::T_RAW);
        $oPayment->oxpayments__oxaddsumrules = new Field('31', Field::T_RAW);
        $oPayment->oxpayments__oxfromboni = new Field('0', Field::T_RAW);
        $oPayment->oxpayments__oxfromamount = new Field('0', Field::T_RAW);
        $oPayment->oxpayments__oxtoamount = new Field('1000000', Field::T_RAW);
        $oPayment->oxpayments__oxchecked = new Field(0, Field::T_RAW);
        $oPayment->oxpayments__oxsort = new Field('-200', Field::T_RAW);
        $oPayment->oxpayments__oxtspaymentid = new Field('', Field::T_RAW);

        // set multi language fields
        foreach ($aLang as $oLang) {
            $iLanguageId = $oLang->id;
            $sPaymentTitle = Registry::getLang()->translateString('PAYOLUTION_INSTALL_PAYMENT_INVOICE_B2B', $iLanguageId);
            $sTag = Registry::getLang()->getLanguageTag($iLanguageId);
            $oPayment->{'oxpayments__oxdesc' . $sTag} = new Field($sPaymentTitle, Field::T_RAW);
            $oPayment->{'oxpayments__oxvaldesc' . $sTag} = new Field($this->paymentOptions, Field::T_RAW);
        }

        $oPayment->save();
    }

    /**
     * Inserting Payolution Invoice payment
     *
     * @param void
     */
    protected function _savePayolutionInstallment($aLang)
    {
        /** @var $oPayment Payment */
        $oPayment = oxNew(Payment::class);
        $oPayment->load('payolution_installment');
        if ($oPayment->isLoaded()) {
            return;
        }

        $oPayment->setEnableMultilang(false);

        $oPayment->setId('payolution_installment');
        $oPayment->oxpayments__oxactive = new Field(1, Field::T_RAW);
        $oPayment->oxpayments__oxaddsum = new Field(0, Field::T_RAW);
        $oPayment->oxpayments__oxaddsumtype = new Field('abs', Field::T_RAW);
        $oPayment->oxpayments__oxaddsumrules = new Field('31', Field::T_RAW);
        $oPayment->oxpayments__oxfromboni = new Field('0', Field::T_RAW);
        $oPayment->oxpayments__oxfromamount = new Field('0', Field::T_RAW);
        $oPayment->oxpayments__oxtoamount = new Field('1000000', Field::T_RAW);
        $oPayment->oxpayments__oxchecked = new Field(0, Field::T_RAW);
        $oPayment->oxpayments__oxsort = new Field('-200', Field::T_RAW);
        $oPayment->oxpayments__oxtspaymentid = new Field('', Field::T_RAW);

        // set multi language fields
        foreach ($aLang as $oLang) {
            $iLanguageId = $oLang->id;
            $sPaymentTitle = Registry::getLang()->translateString('PAYOLUTION_INSTALL_PAYMENT_INSTALLMENT', $iLanguageId);
            $sTag = Registry::getLang()->getLanguageTag($iLanguageId);
            $oPayment->{'oxpayments__oxdesc' . $sTag} = new Field($sPaymentTitle, Field::T_RAW);
            $oPayment->{'oxpayments__oxvaldesc' . $sTag} = new Field($this->paymentOptions, Field::T_RAW);
        }

        $oPayment->save();
    }

    /**
     * Inserting Direct Debit payment
     *
     * @param array $aLang
     */
    protected function _savePayolutionDD($aLang)
    {
        $sPaymentOxid = 'payolution_dd';
        $oPayment = oxNew(Payment::class);
        $oPayment->load($sPaymentOxid);

        if ($oPayment->isLoaded()) {
            return;
        }

        $oPayment->setEnableMultilang(false);
        $oPayment->setId($sPaymentOxid);
        $oPayment->oxpayments__oxactive = new Field(1, Field::T_RAW);
        $oPayment->oxpayments__oxaddsum = new Field(0, Field::T_RAW);
        $oPayment->oxpayments__oxaddsumtype = new Field('abs', Field::T_RAW);
        $oPayment->oxpayments__oxaddsumrules = new Field('31', Field::T_RAW);
        $oPayment->oxpayments__oxfromboni = new Field('0', Field::T_RAW);
        $oPayment->oxpayments__oxfromamount = new Field('0', Field::T_RAW);
        $oPayment->oxpayments__oxtoamount = new Field('1000000', Field::T_RAW);
        $oPayment->oxpayments__oxchecked = new Field(0, Field::T_RAW);
        $oPayment->oxpayments__oxsort = new Field('-200', Field::T_RAW);
        $oPayment->oxpayments__oxtspaymentid = new Field('', Field::T_RAW);

        // set multi language fields
        foreach ($aLang as $oLang) {
            $iLanguageId = $oLang->id;
            $sPaymentTitle = Registry::getLang()->translateString('PAYOLUTION_INSTALL_PAYMENT_DIRECT_DEBIT', $iLanguageId);
            $sTag = Registry::getLang()->getLanguageTag($iLanguageId);
            $oPayment->{'oxpayments__oxdesc' . $sTag} = new Field($sPaymentTitle, Field::T_RAW);
            $oPayment->{'oxpayments__oxvaldesc' . $sTag} = new Field($this->paymentOptions, Field::T_RAW);
        }

        $oPayment->save();
    }

    /**
     * Get SQL queries from install files and clean them
     *
     * @param $sUpdateSQL
     * @return mixed|string
     */
    protected function getSqlsFromFiles($sUpdateSQL)
    {
        $sUpdateSQLFile = $this->_processFiles();

        if ($sUpdateSQLFile && strlen($sUpdateSQLFile) > 0) {
            if (isset($sUpdateSQL) && strlen($sUpdateSQL)) {
                $sUpdateSQL .= ";\r\n" . $sUpdateSQLFile;
            } else {
                $sUpdateSQL = $sUpdateSQLFile;
            }
        }

        return trim(stripslashes($sUpdateSQL));
    }

    /**
     * Executes given query and returns results.
     *
     * @param $sQuery
     * @return bool
     */
    protected function _executeQuery($sQuery)
    {
        try {
            Db::getDb()->execute($sQuery);

            return true;
        } catch (\Exception $oEx) {

        }

        return false;
    }

    /**
     * Execute given queries and return results.
     *
     * @param array $aQueries
     */
    protected function _executeQueries(array $aQueries)
    {
        for ($i = 0; $i < count($aQueries); $i++) {
            $sUpdateSQL = $this->_prepareSqlQuery($aQueries[$i]);
            if (!$sUpdateSQL) {
                continue;
            }

            $blStop = !$this->_executeQuery($sUpdateSQL);

            if ($blStop) {
                break;
            }
        }
    }

    /**
     * Prepare update query for execution
     * @param $sUpdateSQL string
     * @return bool|string
     */
    protected function _prepareSqlQuery($sUpdateSQL)
    {
        $sUpdateSQL = trim($sUpdateSQL);

        $oStr = getStr();
        if ($oStr->strlen($sUpdateSQL) < 1) {
            return false;
        }

        while ($sUpdateSQL[$oStr->strlen($sUpdateSQL) - 1] == ";") {
            $sUpdateSQL = $oStr->substr($sUpdateSQL, 0, ($oStr->strlen($sUpdateSQL) - 1));
        }

        return $sUpdateSQL;
    }

    /**
     * General template parameter setter
     *
     * @param $name
     * @param $value
     */
    protected function setTemplateParam($name, $value)
    {
        $this->_payolutionTemplateParams[$name] = $value;
    }

    /**
     * General template parameter getter
     *
     * @param $name
     * @return null
     */
    public function getTemplateParam($name)
    {
        if (isset($this->_payolutionTemplateParams[$name])) {
            return $this->_payolutionTemplateParams[$name];
        }

        return null;
    }

    /**
     * @return Migrations
     */
    private function setUpMigrationsConfig()
    {
        $oConfig = $this->getConfig();
        $sPath = 'modules/tc/payolution/migrations';

        $migrationsBuilder = new MigrationsBuilder();
        $config = new ConfigFile();
        $config->setVar(ConfigFile::PARAMETER_SOURCE_PATH, $oConfig->getConfigParam('sShopDir') . 'modules/tc/payolution');
        $moduleFacts = new Facts($oConfig->getConfigParam('sShopDir') . $sPath, $config);

        return $migrationsBuilder->build($moduleFacts);
    }

}
