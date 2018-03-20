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

/**
 * Class TransactionType
 * @package TopConcepts\Payolution\Client\Type
 */
class TransactionType implements SerializableInterface
{
    /**
     * @var string
     */
    public $channel;

    /**
     * @var string
     */
    public $mode;

    /**
     * @var string
     */
    public $login;

    /**
     * @var string
     */
    public $pwd;

    /**
     * @var IdentificationType
     */
    public $identification;

    /**
     * @var PaymentType
     */
    public $payment;

    /**
     * @var CustomerType
     */
    public $customer;

    /**
     * @var AccountType
     */
    public $account;

    /**
     * @var AnalysisType
     */
    public $analysis;

    /**
     * TransactionType constructor.
     */
    public function __construct()
    {
        $this->identification = oxNew(IdentificationType::class);
        $this->payment        = oxNew(PaymentType::class);
        $this->customer       = oxNew(CustomerType::class);
        $this->account        = oxNew(AccountType::class);
        $this->analysis       = oxNew(AnalysisType::class);
    }

    /**
     * @param \SimpleXMLElement $output
     */
    public function toXml(\SimpleXMLElement &$output)
    {
        $element =& $output->Transaction;
        $element['channel'] = $this->channel;
        $element['mode']    = $this->mode;

        if (isset($this->login)) {
            $element->User['pwd']   = $this->pwd;
            $element->User['login'] = $this->login;
        }

        $this->identification->toXml($element);
        $this->payment->toXml($element);
        $this->customer->toXml($element);
        $this->account->toXml($element);
        $this->analysis->toXml($element);
    }
}
