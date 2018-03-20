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
 * Class NameType
 * @package TopConcepts\Payolution\Client\Type\Customer
 */
class NameType implements SerializableInterface
{
    /**
     * (required) Surname
     *
     * @var string
     */
    public $family;

    /**
     * (required) Name
     *
     * @var string
     */
    public $given;

    /**
     * (required | optional for b2b)  format yyyy-mm-dd
     *
     * @var string
     */
    public $birthdate;

    /**
     * (optional) Gender - M - male  | F - female (one character)
     *
     * @var string
     */
    public $sex;

    /**
     * (optional) Title    e.g. "Dr."  "Prof. "Jr."
     *
     * @var string
     */
    public $title;

    /**
     * @param \SimpleXMLElement $output
     */
    public function toXml(\SimpleXMLElement &$output)
    {
        if ($this->isNotEmpty()) {
            $element =& $output->Name;
            $element->Family    = $this->family;
            $element->Given     = $this->given;
            $element->Birthdate = $this->birthdate;
            if ($this->sex) {
                $element->Sex = $this->sex;
            }

            if ($this->title) {
                $element->Title = $this->title;
            }
        }
    }

    /**
     * @return bool
     */
    public function isNotEmpty()
    {
        return $this->family || $this->given || $this->birthdate || $this->sex || $this->title;
    }
}
