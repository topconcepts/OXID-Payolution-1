<?php
/**
 * Copyright 2017 Payolution GmbH
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
namespace TopConcepts\Payolution\Client\Type\Analysis;

use TopConcepts\Payolution\Client\Type\Customer\CompanyTypes;

/**
 * Class CompanyType
 * @package TopConcepts\Payolution\Client\Type\Analysis
 */
class CompanyType implements AnalysisTypeInterface
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $uid;

    /**
     * @var string
     */
    public $type;

    /**
     * @var string
     */
    public $ownerFamily;

    /**
     * @var string
     */
    public $ownerGiven;

    /**
     * @var string
     */
    public $ownerBirthdate;

    /**
     * Method converts all object into associative array
     *
     * @return array
     */
    public function toArray()
    {
        $data = [
            'NAME' => $this->name,
            'UID' => $this->uid,
            'TYPE' => $this->type,
        ];

        if ($this->type == CompanyTypes::TYPE_SOLE) {
            $data = array_merge(
                $data,
                [
                    'OWNER_FAMILY' => $this->ownerFamily,
                    'OWNER_GIVEN' => $this->ownerGiven,
                    'OWNER_BIRTHDATE' => $this->ownerBirthdate,
                ]
            );
        }

        return $data;
    }
}
