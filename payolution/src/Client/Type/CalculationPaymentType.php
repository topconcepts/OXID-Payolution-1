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
namespace TopConcepts\Payolution\Client\Type;

/**
 * Class CalculationPaymentType
 * @package TopConcepts\Payolution\Client\Type
 */
class CalculationPaymentType extends PaymentType
{
    /**
     * @param \SimpleXMLElement $output
     */
    public function toXml(\SimpleXMLElement &$output)
    {
        if ($this->isNotEmpty()) {
            $payment =& $output->Payment;
            $payment['code'] = $this->code;
            $paymentPresentation =& $payment->Presentation;
            $paymentPresentation->Currency = $this->currency;
            $paymentPresentation->Usage    = $this->usage;
            $paymentPresentation->Amount   = $this->formattedAmount();

            $output->Account->Brand = 'PAYOLUTION_INS';
        }
    }
}
