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
namespace TopConcepts\Payolution\Module\Model;

use OxidEsales\Eshop\Core\Model\BaseModel;
use OxidEsales\Eshop\Core\Field;

/**
 * Payolution model class for table 'payo_logs'
 *
 * Class LogModel
 * @package TopConcepts\Payolution\Module\Model
 */
class LogModel extends BaseModel
{
    /**
     * LogModel constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->init('payo_logs');
    }

    /**
     * @return LogModel
     */
    public static function create()
    {
        return oxNew(self::class);
    }

    /**
     * @param $value
     *
     * @return Field
     */
    public function setRequest($value)
    {
        return $this->payo_logs__request = new Field($value, Field::T_RAW);
    }

    /**
     * @return string
     */
    public function getRequest()
    {
        return (string)$this->payo_logs__request->value;
    }

    /**
     * @param $value
     *
     * @return Field
     */
    public function setResponse($value)
    {
        return $this->payo_logs__response = new Field($value, Field::T_RAW);
    }

    /**
     * @return string
     */
    public function getResponse()
    {
        return (string)$this->payo_logs__response->value;
    }

    /**
     * @param $value
     *
     * @return Field
     */
    public function setAddedAt($value)
    {
        return $this->payo_logs__added_at = new Field($value, Field::T_RAW);
    }

    /**
     * @return string
     */
    public function getAddedAt()
    {
        return (string)$this->payo_logs__added_at->value;
    }

    /**
     * @return string
     */
    public function getOrderId()
    {
        return (string)$this->payo_logs__order_id->value;
    }

    /**
     * @return string
     */
    public function getOrderNo()
    {
        return (string)$this->payo_logs__order_no->value;
    }

    /**
     * @param $value
     *
     * @return void
     */
    public function setOrderId($value)
    {
        $this->payo_logs__order_id = new Field($value, Field::T_RAW);
    }

    /**
     * @param $value
     */
    public function setOrderNo($value)
    {
        $this->payo_logs__order_no = new Field($value, Field::T_RAW);
    }
}
