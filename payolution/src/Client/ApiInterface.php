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
namespace TopConcepts\Payolution\Client;

use TopConcepts\Payolution\Client\Response\CalculationResponse;
use TopConcepts\Payolution\Client\Response\Response;
use TopConcepts\Payolution\Client\Type\Analysis\ItemType;
use TopConcepts\Payolution\Client\Type\CustomerType;
use TopConcepts\Payolution\Client\Type\PaymentType;
use TopConcepts\Payolution\Client\Type\PriceType;
use TopConcepts\Payolution\Payment\PaymentMethod;

/**
 * WS wrapper to communicate with Payolution servers
 *
 * Interface ApiInterface
 */
interface ApiInterface
{
    /**
     * @param PaymentMethod $paymentMethod
     * @param CustomerType $customer
     * @param PaymentType $payment
     * @param ItemType[] $basketItems
     * @return Response
     */
    public function precheck(PaymentMethod $paymentMethod, CustomerType $customer, PaymentType $payment, $basketItems);

    /**
     * @param string $precheckId
     * @param PaymentMethod $paymentMethod
     * @param CustomerType $customer
     * @param PaymentType $payment
     * @param ItemType[] $basketItems
     * @return Response
     */
    public function preauth($precheckId, PaymentMethod $paymentMethod, CustomerType $customer, PaymentType $payment, $basketItems);

    /**
     * PZ: Update of order number or change amount before shipment
     *
     * @param $preauthResponseId
     * @param PaymentMethod $paymentMethod
     * @param PaymentType $payment
     * @param $basketItems
     *
     * @return mixed
     */
    public function update($preauthResponseId, PaymentMethod $paymentMethod, PaymentType $payment, $basketItems);

    /**
     * @param string $preauthResponseId
     * @param PaymentType $payment
     * @return Response
     */
    public function capture($preauthResponseId, PaymentType $payment);

    /**
     * @param string $preauthResponseId
     * @param PaymentType $payment
     * @return Response
     */
    public function refund($preauthResponseId, PaymentType $payment);

    /**
     * @param string $preauthResponseId
     * @param PaymentType $payment
     * @return Response
     */
    public function reverse($preauthResponseId, PaymentType $payment);

    /**
     * @param PriceType $price
     * @param string  $country
     *
     * @return CalculationResponse
     */
    public function calculate(PriceType $price, $country);
}
