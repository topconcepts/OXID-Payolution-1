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

use TopConcepts\Payolution\Client\SerializableInterface;

/**
 * Class AddressType
 * @package TopConcepts\Payolution\Client\Type\Customer
 */
class AddressType implements SerializableInterface
{
    /**
     * (required) Country  two characters ISO code (in uppercase)
     *
     * @var string
     */
    public $country;

    /**
     * (required) City
     *
     * @var string
     */
    public $city;

    /**
     * (required) Zip code  (numeric)
     *
     * @var string
     */
    public $zip;

    /**
     * (required) street including street number
     *
     * @var string
     */
    public $street;

    /**
     * (optional) State
     *
     * @var string
     */
    public $state;

    /**
     * @param \SimpleXMLElement $output
     */
    public function toXml(\SimpleXMLElement &$output)
    {
        if ($this->isNotEmpty()) {
            $element =& $output->Address;
            $element->Country = $this->country;
            $element->City    = $this->city;
            $element->Zip     = $this->zip;
            $element->Street  = $this->street;
            if ($this->state) {
                $element->State = $this->state;
            }
        }
    }

    /**
     * @return bool
     */
    public function isNotEmpty()
    {
        return $this->country || $this->city || $this->zip || $this->street || $this->state;
    }
}
