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

use TopConcepts\Payolution\Client\Request\AbstractRequest;
use TopConcepts\Payolution\Client\Request\CalculateRequest;
use TopConcepts\Payolution\Client\Request\CaptureRequest;
use TopConcepts\Payolution\Client\Request\PreAuthRequest;
use TopConcepts\Payolution\Client\Request\PreCheckRequest;
use TopConcepts\Payolution\Client\Request\PzRequest;
use TopConcepts\Payolution\Client\Request\RefundRequest;
use TopConcepts\Payolution\Client\Request\ReverseRequest;
use TopConcepts\Payolution\Client\Response\ErrorResponse;
use TopConcepts\Payolution\Client\Response\Response;
use TopConcepts\Payolution\Client\Type\Analysis\ItemType;
use TopConcepts\Payolution\Client\Type\ConfigType;
use TopConcepts\Payolution\Client\Type\CustomerType;
use TopConcepts\Payolution\Client\Type\PaymentType;
use TopConcepts\Payolution\Client\Type\PriceType;
use TopConcepts\Payolution\Payment\PaymentMethod;

/**
 * Class Client
 * @package TopConcepts\Payolution\Client
 */
class Client implements ApiInterface
{
    /**
     * @var ConfigType
     */
    private $configuration;

    /**
     * @var WebService
     */
    private $ws;

    /**
     * @param WebService $webService
     * @param array $configParameters
     */
    public function __construct(WebService $webService, array $configParameters)
    {
        $this->ws = $webService;
        $this->configuration = oxNew(ConfigType::class, $configParameters);
    }

    /**
     * @param PaymentMethod $paymentMethod
     * @param CustomerType $customer
     * @param PaymentType $payment
     * @param Type\Analysis\ItemType[] $basketItems
     *
     * @return Response
     */
    public function precheck(PaymentMethod $paymentMethod, CustomerType $customer, PaymentType $payment, $basketItems)
    {
        /* @var $request PreCheckRequest */
        $request = oxNew(PreCheckRequest::class, $paymentMethod, $customer, $payment, $basketItems);

        return $this->call($request);
    }

    /**
     * @param string $precheckId
     * @param PaymentMethod $paymentMethod
     * @param CustomerType $customer
     * @param PaymentType $payment
     * @param ItemType[] $basketItems
     *
     * @return Response
     */
    public function preauth($precheckId, PaymentMethod $paymentMethod, CustomerType $customer, PaymentType $payment, $basketItems)
    {
        /* @var $request PreAuthRequest */
        $request = oxNew(PreAuthRequest::class, $precheckId, $paymentMethod, $customer, $payment, $basketItems);

        return $this->call($request);
    }

    /**
     * @param $preauthResponseId
     * @param PaymentMethod $paymentMethod
     * @param PaymentType $payment
     * @param $basketItems
     *
     * @return Response
     */
    public function update($preauthResponseId, PaymentMethod $paymentMethod, PaymentType $payment, $basketItems)
    {
        /* @var $request PzRequest */
        $request = oxNew(PzRequest::class, $preauthResponseId, $paymentMethod, $payment, $basketItems);

        return $this->call($request);
    }

    /**
     * @param string $preauthResponseId
     * @param PaymentType $payment
     *
     * @return Response
     */
    public function capture($preauthResponseId, PaymentType $payment)
    {
        /* @var $request CaptureRequest */
        $request = oxNew(CaptureRequest::class, $preauthResponseId, $payment);

        return $this->call($request);
    }

    /**
     * @param string $preauthResponseId
     * @param PaymentType $payment
     *
     * @return Response
     */
    public function refund($preauthResponseId, PaymentType $payment)
    {
        /* @var $request RefundRequest */
        $request = oxNew(RefundRequest::class, $preauthResponseId, $payment);

        return $this->call($request);
    }

    /**
     * @param string $preauthResponseId
     * @param PaymentType $payment
     *
     * @return Response
     */
    public function reverse($preauthResponseId, PaymentType $payment)
    {
        /* @var $request ReverseRequest */
        $request = oxNew(ReverseRequest::class, $preauthResponseId, $payment);

        return $this->call($request);
    }

    /**
     * @param PriceType $price
     * @param string $country
     *
     * @return Response
     */
    public function calculate(PriceType $price, $country)
    {
        /* @var $request CalculateRequest */
        $request = oxNew(CalculateRequest::class, $price, $country);

        return $this->call($request);
    }

    /**
     * @param AbstractRequest $request
     *
     * @return Response
     */
    private function call(AbstractRequest $request)
    {
        $transaction = $request->transaction();
        $transaction->channel = $this->configuration->channel;

        if ($request instanceof CalculateRequest) {
            $transaction->payment->invoiceId = md5(mt_rand());
        }

        $transaction->login = $this->configuration->login;
        $transaction->pwd   = $this->configuration->pass;
        $url               = $this->configuration->xml_url;
        $transaction->mode = $this->configuration->mode;
        $parameter         = 'load';
        $envelope          = $this->createEnvelope();

        if ($transaction->payment->invoiceId) {
            $transaction->identification->transactionId = $transaction->payment->invoiceId;
        }

        $transaction->identification->shopperId = $transaction->customer->username;
        $transaction->identification->invoiceId = $transaction->payment->invoiceId;

        $transaction->analysis->webshopUrl         = $this->configuration->shopUrl;
        $transaction->analysis->invoiceId          = $transaction->payment->invoiceId;
        $transaction->analysis->customer->language = $transaction->customer->getCustomerFullLanguage();

        $transaction->toXml($envelope);

        $response = $this->ws->post($url, [$parameter => $envelope->asXML()]);

        try {
            $responseXml = new \SimpleXMLElement($response);
        } catch (\Exception $e) {
            // Return connection exception if ws response not valid xml

            /** @var ErrorResponse $error */
            $error = oxNew(ErrorResponse::class);
            $error->status = ErrorResponse::STATUS_CONNECTION_ERROR;

            /** @var Response $response */
            $response = oxNew(Response::class);
            $response->setError($error);
            return $response;
        }

        return $request->convertResponse($responseXml);
    }

    /**
     * @return \SimpleXMLElement
     */
    private function createEnvelope()
    {
        $xml = simplexml_load_string('<?xml version="1.0" encoding="UTF-8"?><Request/>');
        $xml['version'] = '1.0';

        $xml->Header->Security['sender'] = $this->configuration->sender;

        return $xml;
    }
}
