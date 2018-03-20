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

//    /**
//     * @param  $customer
//     * @param \SimpleXMLElement $output
//     *
//     * @return \SimpleXMLElement
//     */
//    protected function customerToXml($customer, \SimpleXMLElement $output)
//    {
//
//        // Customer details
//        $xmlCustomer                  = $output->Customer;
//        $xmlCustomer->Name->Company   = $customer->company;
//        $xmlCustomer->Name->Family    = $customer->lastname;
//        $xmlCustomer->Name->Given     = $customer->firstname;
//        $xmlCustomer->Name->Sex       = $customer->getCustomerGender();
//        $xmlCustomer->Name->Birthdate = $customer->birthdate;
//
//        // Customer details (Contact)
//        $xmlCustomer->Contact->Email = $customer->email;
//        $xmlCustomer->Contact->Phone = $customer->telephone;
//        $xmlCustomer->Contact->Ip    = $customer->getIp();
//
//        // Address
//        $xmlCustomer->Address->City    = $customer->getAddress()->city;
//        $xmlCustomer->Address->Country = $customer->getAddress()->country_id;
//        $xmlCustomer->Address->Street = $customer->getAddress()->street;
//        $xmlCustomer->Address->Zip    = $customer->getAddress()->postcode;
//
//        return $output;
//    }
//
//    /**
//     * @param Payolution_Client_Type_Payment $payment
//     * @param SimpleXMLElement               $output
//     *
//     * @return SimpleXMLElement
//     */
//    protected function paymentToXml(
//      Payolution_Client_Type_Payment $payment,
//      SimpleXMLElement $output
//    ) {
//        if ($payment->operationType && $payment->paymentType) {
//            $output->Payment->OperationType = $payment->operationType;
//            $output->Payment->PaymentType   = $payment->paymentType;
//        }
//
//        $xmlPresentation = $output->Payment->Presentation;
//
//        $xmlPresentation->Amount   = $this->formatPrice($payment->amount);
//        $xmlPresentation->Currency = $payment->currency;
//        $xmlPresentation->Usage    = $payment->usage;
//
//        return $output;
//    }

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
