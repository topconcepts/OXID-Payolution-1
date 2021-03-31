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
namespace TopConcepts\Payolution\Client\Request;

use TopConcepts\Payolution\Client\Response\Response;
use TopConcepts\Payolution\Client\Type\TransactionType;
use TopConcepts\Payolution\Payment\PaymentMethod;

/**
 * Class AbstractRequest
 * @package TopConcepts\Payolution\Client\Request
 */
abstract class AbstractRequest
{
    /**
     * @return
     */
    private $_transaction;

    /**
     * @var PaymentMethod
     */
    private $_paymentMethod;

    /**
     * @return TransactionType
     */
    public function transaction()
    {
        if (!$this->_transaction) {
            $this->_transaction = oxNew(TransactionType::class);
        }

        return $this->_transaction;
    }

    /**
     * @return PaymentMethod
     */
    public function paymentMethod()
    {
        return $this->_paymentMethod;
    }

    /**
     * Method sets payment method of this request
     * You must explicitly call this method on each constructor!
     *
     * @param PaymentMethod $paymentMethod
     */
    protected function setPaymentMethod(PaymentMethod $paymentMethod)
    {
        $this->_paymentMethod = $paymentMethod;
    }

    /**
     * @param \SimpleXMLElement $xml
     *
     * @return Response
     */
    public function convertResponse($xml)
    {
        return oxNew(Response::class)->parse($xml);
    }

    /**
     * Format price for Cash-Ticket (format 0.00)
     *
     * @param string $amount
     *
     * @return string
     */
    protected function formatPrice($amount)
    {
        $formatted = number_format($amount, 2, '.', '');

        return sprintf('%.2f', $formatted);
    }

}
