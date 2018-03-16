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
namespace Payolution\Client\Type\Customer;

use Payolution\Client\SerializableInterface;

/**
 * Class ContactType
 * @package Payolution\Client\Type\Customer
 */
class ContactType implements SerializableInterface
{
    /**
     * (required) Customer's email address
     *
     * @var string
     */
    public $email;

    /**
     * (required) Customer's IP address
     * IP address of the customer in the format 000.000.000.000 Special care
     * needs to be taken with load balancers. Also for reverse proxies.
     *
     * @var string
     */
    public $ip;

    /**
     * (optional) Phone e.g. +4348943849
     *
     * @var string
     */
    public $phone;

    /**
     * (optional) Mobile phone  e.g. +4348943849
     *
     * @var string
     */
    public $mobile;

    /**
     * @param \SimpleXMLElement $output
     */
    public function toXml(\SimpleXMLElement &$output)
    {
        if ($this->isNotEmpty()) {
            $element =& $output->Contact;
            $element->Email = $this->email;
            $element->Ip    = $this->ip;
            if ($this->phone) {
                $element->Phone = $this->phone;
            }
            if ($this->mobile) {
                $element->Mobile = $this->mobile;
            }
        }
    }

    /**
     * @return bool
     */
    public function isNotEmpty()
    {
        return $this->email || $this->ip || $this->phone || $this->mobile;
    }
}
