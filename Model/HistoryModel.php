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
namespace Payolution\Module\Model;

use OxidEsales\Eshop\Core\Model\BaseModel;
use OxidEsales\Eshop\Core\Field;

/**
 * Payolution model class for table 'payo_history'
 *
 * Class HistoryModel
 * @package Payolution\Module\Model
 */
class HistoryModel extends BaseModel
{
    /**
     * HistoryModel constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->init('payo_history');
    }

    /**
     * @return HistoryModel
     */
    public static function create()
    {
        return oxNew(self::class);
    }

    /**
     * @param $value
     * @return $this
     */
    public function setOrderId($value)
    {
        $this->payo_history__order_id = new Field($value, Field::T_RAW);

        return $this;
    }

    /**
     * @return string
     */
    public function getOrderId()
    {
        return (string)$this->payo_history__order_id->value;
    }

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setStatus($value)
    {
        $this->payo_history__status = new Field($value, Field::T_RAW);

        return $this;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return (string)$this->payo_history__status->value;
    }

    /**
     * @param array $value
     *
     * @return $this
     */
    public function setHistoryValues($value)
    {
        $this->payo_history__history_values = new Field(json_encode($value), Field::T_RAW);

        return $this;
    }

    /**
     * @return array
     */
    public function getHistoryValues()
    {
        $valuesJson = (string)$this->payo_history__history_values->value;

        $values = @json_decode($valuesJson);

        return $values ? $values : array();
    }

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setAddedAt($value)
    {
        $this->payo_history__added_at = new Field($value, Field::T_RAW);

        return $this;
    }

    /**
     * @return string
     */
    public function getAddedAt()
    {
        return (string)$this->payo_history__added_at->value;
    }

    /**
     * @param $value
     *
     * @return $this
     */
    public function setAddedBy($value)
    {
        $this->payo_history__added_by = new Field($value, Field::T_RAW);

        return $this;
    }

    /**
     * @return string
     */
    public function getAddedBy()
    {
        return (string)$this->payo_history__added_by->value;
    }
}
