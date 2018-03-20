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
namespace TopConcepts\Payolution\Client\Type;

use TopConcepts\Payolution\Client\SerializableInterface;
use TopConcepts\Payolution\Client\Type\Analysis\ShippingType;
use TopConcepts\Payolution\Client\Type\Customer\CompanyType;
use TopConcepts\Payolution\Client\Type\Customer\AddressType;
use TopConcepts\Payolution\Client\Type\Customer\ContactType;
use TopConcepts\Payolution\Client\Type\Customer\NameType;

/**
 * Class CustomerType
 * @package TopConcepts\Payolution\Client\Type
 */
class CustomerType implements SerializableInterface
{
    /**
     * @var NameType
     */
    public $name;

    /**
     * @var AddressType
     */
    public $address;

    /**
     * @var ContactType
     */
    public $contact;

    /**
     * @var CompanyType
     */
    public $company;

    /**
     * (optional) customer username in system
     * This property is not directly stored inside this XML
     *
     * @var string
     */
    public $username;

    /**
     * (optional) customer`s language in system
     * This property is not directly stored inside this XML
     * @var string  two character country code.
     */
    public $customerLanguage;

    /**
     * @var string
     */
    public $customerFrontendLanguage;

    /**
     * (optional) customer`s shipping information
     * This property is not directly stored inside this XML
     *
     * @var ShippingType
     */
    public $shippingAddress;

    /**
     * (optional)
     * @var bool
     */
    public $isRegistered;

    /**
     * (optional)
     * @var string
     */
    public $registerDate;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->name    = oxNew(NameType::class);
        $this->address = oxNew(AddressType::class);
        $this->contact = oxNew(ContactType::class);
        $this->company = oxNew(CompanyType::class);
    }

    /**
     * @param \SimpleXMLElement $output
     *
     * @return void
     */
    public function toXml(\SimpleXMLElement &$output)
    {
        if ($this->isNotEmpty()) {
            $customer =& $output->Customer;
            $this->name->toXml($customer);
            $this->address->toXml($customer);
            $this->contact->toXml($customer);
            $this->company->toXml($customer);
        }
    }

    /**
     * @return bool
     */
    public function isNotEmpty()
    {
        return ($this->name->isNotEmpty() || $this->address->isNotEmpty() ||
          $this->contact->isNotEmpty() || $this->company->isNotEmpty());
    }

    /**
     * @return string
     */
    public function getCustomerFullLanguage()
    {
        return $this->customerFrontendLanguage . '_' . $this->customerLanguage;
    }
}
