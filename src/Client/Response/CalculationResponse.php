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
namespace TopConcepts\Payolution\Client\Response;

/**
 * Class CalculationResponse
 * @package TopConcepts\Payolution\Client\Response
 */
class CalculationResponse extends Response
{
    /**
     * @var array
     */
    private $_range;

    /**
     * @var array
     */
    private $_installmentInfo;

    /**
     * @var string
     */
    private $_uniqueId;

    /**
     * @param \SimpleXMLElement $xml
     *
     * @return $this
     */
    public function parse(\SimpleXMLElement $xml)
    {

        if ((string) $xml->Status == 'ERROR') {
            $responseError = new ErrorResponse();
            $responseError->status = (string) $xml->Status;
            $responseError->statusCode = (string) $xml->StatusCode;
            $responseError->message = (string) $xml->Description;
            $responseError->messageCode = $responseError->statusCode;
            $this->setError($responseError);
        } else {
            $this->parseSuccessXml($xml);
        }

        return $this;
    }

    /**
     * @param \SimpleXMLElement $xml
     */
    private function parseSuccessXml(\SimpleXMLElement $xml)
    {
        $transaction = $xml->Transaction;

        foreach ($transaction->Payment->PaymentDetails->PaymentPlan as $details) {
            $this->_range[] = (string) $details->Duration;
        }

        $this->_installmentInfo = [];
        $this->_uniqueId = (string) $transaction->Identification->UniqueID;

        foreach ($transaction->Payment->PaymentDetails->PaymentPlan as $details) {
            $duration = (string) $details->Duration;
            $installments = [];
            $installmentAmount = null;

            foreach ($details->Installment as $installment) {
                $installments [] = array(
                  'amount'  => (string) $installment->Amount,
                  'dueDate' => (string) $installment->Due,
                );

                if (!$installmentAmount) {
                    $installmentAmount = (string) $installment->Amount;
                }
            }

            $this->_installmentInfo[$duration] = [
              'installmentAmount' => $installmentAmount,
              'interestRate'      => (string) $details->InterestRate,
              'effectiveInterest' => (string) $details->EffectiveInterestRate,
              'totalAmount'       => (string) $details->TotalAmount,
              'installments'      => $installments,
              'url'               => (string) $details->StandardCreditInformationUrl,
              'privacyUrl'        => (string) $transaction->AdditionalInformation->DataPrivacyConsentUrl,
              'termsUrl'          => (string) $transaction->AdditionalInformation->TacUrl,
            ];
        }
    }

    /**
     * @return array
     */
    public function range()
    {
        return $this->_range;
    }

    /**
     * @return array  Assoc. array is in the same format as Payolution
     *                javascript  Payolution.calculateInstallment(price,
     *                duration) return value;
     */
    public function installmentInfo()
    {
        return $this->_installmentInfo;
    }

    /**
     * @return string
     */
    public function uniqueId()
    {
        return $this->_uniqueId;
    }
}
