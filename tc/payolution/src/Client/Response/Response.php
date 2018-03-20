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
namespace TopConcepts\Payolution\Client\Response;

/**
 * Class Response
 * @package TopConcepts\Payolution\Client\Response
 */
class Response
{
    /**
     * @var bool
     */
    private $_success = true;

    /**
     * @var ErrorResponse
     */
    private $_error;

    /**
     * Response ID is an ID which is returned by Payolution in xml:
     * //Response/Transaction/Identification/UniqueId
     *
     * @var string
     */
    private $_responseId;

    /**
     * @var string
     */
    private $_paymentReferenceId;

    /**
     *
     */
    public function __construct()
    {
    }

    /**
     * @param \SimpleXMLElement $xml
     *
     * @return Response
     */
    public function parse(\SimpleXMLElement $xml)
    {
        $this->parseXml($xml);

        return $this;
    }

    /**
     * @param \SimpleXMLElement $xml
     */
    private function parseXml(\SimpleXMLElement $xml)
    {
        $reference = $xml->Transaction->Processing->ConnectorDetails->xpath('//Result[@name="PaymentReference"]');
        $this->_responseId = (string) $xml->Transaction->Identification->UniqueID;
        $this->_paymentReferenceId = count($reference) > 0 ? (string) $reference[0] : '';
        $responseStatus = (string) $xml->Transaction->Processing->Result;
        $this->_success = ($responseStatus == 'ACK');

        if ($this->_success) {
            $this->onSuccess($xml);
        } else {
            $this->_error = ErrorResponse::createFromXml($xml->Transaction->Processing);
            $this->onError($xml);
        }
    }

    /**
     * Override this method to implemented your own logic then response is
     * successful
     *
     * @param \SimpleXMLElement $xml
     */
    protected function onSuccess($xml)
    {
    }

    /**
     * Override this method to implemented your own logic then response is
     * erroneous
     *
     * @param \SimpleXMLElement $xml
     */
    protected function onError($xml)
    {
    }

    /**
     * @param ErrorResponse $error
     */
    public function setError(ErrorResponse $error)
    {
        $this->_success = false;
        $this->_error   = $error;
    }

    /**
     * @return bool
     */
    public function success()
    {
        return $this->_success;
    }

    /**
     * @return string
     */
    public function responseId()
    {
        return $this->_responseId;
    }

    /**
     * @return string
     */
    public function paymentReferenceId()
    {
        return $this->_paymentReferenceId;
    }

    /**
     * @return ErrorResponse
     */
    public function error()
    {
        return $this->_error;
    }
}
