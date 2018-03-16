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
namespace Payolution\Client\Request;

use Payolution\Client\Response\Response;
use Payolution\Client\Type\PaymentType;

/**
 * Class ReverseRequest
 * @package Payolution\Client\Request
 */
class ReverseRequest extends AbstractRequest
{
    /**
     * @param string $preauthResponseId
     * @param PaymentType $payment
     */
    public function __construct($preauthResponseId, PaymentType $payment)
    {
        $this->setPaymentMethod($payment->paymentMethod);
        $transaction = $this->transaction();
        $transaction->identification->referenceId = $preauthResponseId;
        $transaction->payment = $payment;
        $transaction->payment->code = PaymentType::REVERSAL;
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
}
