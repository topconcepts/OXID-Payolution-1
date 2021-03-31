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
namespace TopConcepts\Payolution\Client\Type;

use OxidEsales\Eshop\Core\Module\Module;
use OxidEsales\Eshop\Core\Registry;
use TopConcepts\Payolution\Client\SerializableInterface;
use TopConcepts\Payolution\Client\Type\Analysis\AnalysisTypeInterface;
use TopConcepts\Payolution\Client\Type\Analysis\AccountType as AnalysisAccountType;
use TopConcepts\Payolution\Client\Type\Analysis\CompanyType;
use TopConcepts\Payolution\Client\Type\Analysis\CustomerType as AnalysisCustomerType;
use TopConcepts\Payolution\Client\Type\Analysis\ItemType;
use TopConcepts\Payolution\Client\Type\Analysis\ShippingType;
use TopConcepts\Payolution\Client\Type\Analysis\TargetCountryType;
use TopConcepts\Payolution\Client\Type\Analysis\TransportationType;
use TopConcepts\Payolution\Client\Utils;

/**
 * Class AnalysisType
 * @package TopConcepts\Payolution\Client\Type
 */
class AnalysisType implements SerializableInterface, AnalysisTypeInterface
{
    const SYSTEM_VENDOR = 'PAYOLUTION_PHP_XML';
    const SESSION_KEY = 'SESSION_ID';
    const SYSTEM_TYPE = 'webshop';
    const SYSTEM_VERSION = 1;
    const COMPANY_PREFIX = 'COMPANY_';
    const CUSTOMER_PREFIX = 'CUSTOMER_';
    const ACCOUNT_PREFIX = 'ACCOUNT_';
    const MODULE_NAME = 'OXID Payolution module';

    /**
     * @var ShippingType
     */
    public $shipping;

    /**
     * @var TransportationType
     */
    public $transportation;

    /**
     * @var ItemType[]|array
     */
    public $items = [];

    /**
     * @var string
     */
    public $taxAmount;

    /**
     * @var string
     */
    public $preCheck;

    /**
     * @var string
     */
    public $preCheckId;

    /**
     * @var string
     */
    public $invoiceId;

    /**
     * @var string  must be 'B2B' or empty
     */
    public $trxType;

    /**
     * @var string
     */
    public $companyName;

    /**
     * @var string
     */
    public $companyUid;

    /**
     * @var string
     */
    public $installmentAmount;

    /**
     * @var string
     */
    public $duration;

    /**
     * Reference to the Calculation Response.<UniqueID>Tx-..... (from CL
     * response)
     *
     * @var string
     */
    public $calculationId;

    /**
     * @var string
     */
    public $webshopUrl;

    /**
     * @var AccountType
     */
    public $account;

    /**
     * @var CustomerType
     */
    public $customer;

    /**
     * @var CompanyType
     */
    public $company;

    /**
     * @var TargetCountryType
     */
    public $targetCountry;

    /**
     * @var true
     */
    public $needToUseSessionId = false;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->shipping       = oxNew(ShippingType::class);
        $this->transportation = oxNew(TransportationType::class);
        $this->account        = oxNew(AnalysisAccountType::class);
        $this->customer       = oxNew(AnalysisCustomerType::class);
        $this->company        = oxNew(CompanyType::class);
        $this->targetCountry  = oxNew(TargetCountryType::class);
    }


    /**
     * @param $description
     * @param $price
     * @param $tax
     * @param $category
     *
     * @return ItemType
     */
    public function addItem($description, $price, $tax, $category)
    {
        $item = oxNew(ItemType::class);
        $item->descr    = $description;
        $item->price    = $price;
        $item->tax      = $tax;
        $item->category = $category;
        $this->items[] = $item;

        return $item;
    }

    /**
     * @param \SimpleXMLElement $output
     */
    public function toXml(\SimpleXMLElement &$output)
    {
        if ($this->isNotEmpty()) {
            $element =& $output->Analysis;
            /* @var $element \SimpleXMLElement */

            foreach ($this->toArray() as $key => $value) {
                $criterion = $element->addChild('Criterion', htmlspecialchars($value, ENT_COMPAT, Registry::getLang()->translateString('charset'), false));
                $criterion['name'] = $key;
            }
        }
    }

    /**
     * @return bool
     */
    public function isNotEmpty()
    {
        return count($this->toArray()) > 0;
    }

    /**
     * @param string $prefix
     * @param array  $array
     * @param string $suffix
     *
     * @return array
     */
    private function prefixKeys($prefix, $array, $suffix = '')
    {
        $results = [];
        foreach ($array as $k => $value) {
            $results[$prefix . $k . $suffix] = $value;
        }

        return $results;
    }

    /**
     * Method converts all object into associative array
     *
     * @return array
     */
    public function toArray()
    {

        $shipping = $this->prefixKeys(
            'SHIPPING_',
            $this->shipping ? $this->shipping->toArray() : []
        );

        $transportation = $this->prefixKeys(
            'TRANSPORTATION_',
            $this->transportation ? $this->transportation->toArray() : ''
        );

        $itemValues = [];
        $no         = 1;

        foreach ($this->items as $i) {
            $itemValues = array_merge($itemValues,
              $this->prefixKeys('ITEM_', $i->toArray(),
                sprintf("_%02d", $no++)));
        }

        $values = [
            'WEBSHOP_URL'                => $this->webshopUrl,
            'TAX_AMOUNT'                 => $this->formatMoneyOrNull($this->taxAmount),
            'PRE_CHECK'                  => $this->preCheck,
            'PRE_CHECK_ID'               => $this->preCheckId,
            'INVOICE_ID'                 => $this->invoiceId ? $this->invoiceId : null,
            'TRX_TYPE'                   => $this->trxType,
            'INSTALLMENT_AMOUNT'         => $this->formatMoneyOrNull($this->installmentAmount),
            'DURATION'                   => $this->duration,
            'CALCULATION_ID'             => $this->calculationId,
            'CALCULATION_TARGET_COUNTRY' => $this->targetCountry->code,
            'REQUEST_SYSTEM_VENDOR'      => self::SYSTEM_VENDOR,
            'REQUEST_SYSTEM_VERSION'     => self::SYSTEM_VERSION,
            'REQUEST_SYSTEM_TYPE'        => self::SYSTEM_TYPE,
            'PAYOLUTION_CALCULATION_TARGET_COUNTRY' => $this->targetCountry->code,
            'MODULE_NAME'                => self::MODULE_NAME,
            'MODULE_VERSION'             => $this->getModuleVersion(),
        ];

        $account  = $this->prefixKeys(self::ACCOUNT_PREFIX, $this->account->toArray());
        $customer = $this->prefixKeys(self::CUSTOMER_PREFIX, $this->customer->toArray());
        $company = $this->prefixKeys(self::COMPANY_PREFIX, $this->company->toArray());

        $list = array_merge($this->getSessionKey(), $shipping, $transportation, $itemValues, $values, $account, $customer, $company);

        $filtered = array_filter($list, [$this, '_filterIsNotNull']);

        return $this->prefixKeys('PAYOLUTION_', $filtered);
    }

    /**
     * @param $amount
     *
     * @return null|string
     */
    private function formatMoneyOrNull($amount)
    {
        return $amount !== null ? Utils::formatMoney($amount) : null;
    }

    /**
     * @return array
     */
    private function getSessionKey()
    {
        if ($this->needToUseSessionId) {
            return [self::SESSION_KEY => Registry::getSession()->getVariable('FraudSessionId')];
        }

        return [];
    }

    /**
     * @param $item
     *
     * @return bool
     */
    protected function _filterIsNotNull($item)
    {
        return $item !== null;
    }

    /**
    * @return string
    */
    public function getModuleVersion()
    {
        $module = new Module();
        $module->load('payolution');

        return $module->getInfo('version');
    }
}
