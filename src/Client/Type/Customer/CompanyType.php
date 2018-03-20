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
namespace TopConcepts\Payolution\Client\Type\Customer;

use TopConcepts\Payolution\Client\SerializableInterface;

/**
 * Class CompanyType
 * @package TopConcepts\Payolution\Client\Type\Customer
 */
class CompanyType implements SerializableInterface
{
    /**
     * (required) Company name
     *
     * @var string
     */
    public $name;

    /**
     * (optional) Company reg-no. Or vat-no.
     *
     * @var string
     */
    public $uid;

    /**
     * (optional) Trade registry number (e.g. FN999999X)
     *
     * @var string
     */
    public $tradeRegistryNumber;

    /**
     * (optional) Company register key (e,g. XXXX)
     *
     * @var
     */
    public $registerKey;

    /**
     * @var string
     */
    public $type;
    
    /**
     * @var string
     */
    public $ownerGiven;

    /**
     * @var string
     */
    public $ownerFamily;

    /**
     * @var string
     */
    public $ownerBirthdate;
    
    /**
     * @param \SimpleXMLElement $output
     */
    public function toXml(\SimpleXMLElement &$output)
    {
        if ($this->isNotEmpty()) {
            $element =& $output->Company;

            $element->Name = $this->name;

            if ($this->uid) {
                $element->UID = $this->uid;
            }

            if ($this->tradeRegistryNumber) {
                $element->TradeRegistryNumber = $this->tradeRegistryNumber;
            }

            if ($this->registerKey) {
                $element->RegisterKey = $this->registerKey;
            }
        }
    }

    /**
     * @return bool
     */
    public function isNotEmpty()
    {
        return $this->name || $this->uid || $this->tradeRegistryNumber || $this->registerKey;
    }
}
