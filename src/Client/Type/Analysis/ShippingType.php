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
namespace Payolution\Client\Type\Analysis;

/**
 * Class ShippingType
 * @package Payolution\Client\Type\Analysis
 */
class ShippingType implements AnalysisTypeInterface
{
    /**
     * @var string
     */
    public $street;

    /**
     * @var string
     */
    public $zip;

    /**
     * @var string
     */
    public $city;

    /**
     * @var string
     */
    public $state;

    /**
     * @var string
     */
    public $country;

    /**
     * First Name
     *
     * @var string
     */
    public $given;

    /**
     * Last Name
     *
     * @var string
     */
    public $family;

    /**
     * @var string
     */
    public $company;

    /**
     * @var string
     */
    public $additional;

    /**
     * one of BRANCH_PICKUP, POST_OFFICE_PICKUP, PACK_STATION
     *
     * @var string
     */
    public $type;

    /**
     * Method converts all object into associative array
     *
     * @return array
     */
    public function toArray()
    {
        return [
          'STREET'     => $this->skipEmpty($this->street),
          'ZIP'        => $this->skipEmpty($this->zip),
          'CITY'       => $this->skipEmpty($this->city),
          'STATE'      => $this->skipEmpty($this->state),
          'COUNTRY'    => $this->skipEmpty($this->country),
          'GIVEN'      => $this->skipEmpty($this->given),
          'FAMILY'     => $this->skipEmpty($this->family),
          'COMPANY'    => $this->skipEmpty($this->company),
          'ADDITIONAL' => $this->skipEmpty($this->additional),
          'TYPE'       => $this->skipEmpty($this->type),
        ];
    }

    /**
     * @param mixed $value
     * @return null|mixed
     */
    private function skipEmpty($value)
    {
        return $value ? $value : null;
    }
}
