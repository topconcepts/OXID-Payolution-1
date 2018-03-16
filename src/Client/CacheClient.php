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
namespace Payolution\Client;

use OxidEsales\Eshop\Core\Registry;
use Payolution\Client\Response\ErrorResponse;
use Payolution\Client\Response\Response;
use Payolution\Client\Type\Analysis\ItemType;
use Payolution\Client\Type\CustomerType;
use Payolution\Client\Type\PaymentType;
use Payolution\Client\Type\PriceType;
use Payolution\Form\InstallmentForm;
use Payolution\Payment\PaymentMethod;

/**
 * Class CacheClient
 * @package Payolution\Client
 */
class CacheClient implements ApiInterface
{
    /**
     * @var string
     */
    private $PERSISTENCE_SESSION_NAME = 'payo_client_api_cache';

    /**
     * @var ApiInterface
     */
    private $api;

    /**
     * @var array assoc array where key is `hash` and value is a cached object.
     */
    private $cacheMap = [];

    /**
     * @param ApiInterface $api
     */
    public function __construct(ApiInterface $api)
    {
        $this->api = $api;

        if ($savedCacheMap = Registry::getSession()->getVariable($this->PERSISTENCE_SESSION_NAME)) {
            $this->cacheMap = $savedCacheMap;
        }
    }

    /**
     * @param PaymentMethod $paymentMethod
     * @param CustomerType $customer
     * @param PaymentType $payment
     * @param ItemType[] $basketItems
     *
     * @return mixed|null|Response
     */
    public function precheck(PaymentMethod $paymentMethod, CustomerType $customer, PaymentType $payment, $basketItems)
    {
        // *) we cache only by payment fields which are submitted to server.
        $paymentXml = new \SimpleXMLElement('<Payment></Payment>');
        $payment->toXml($paymentXml);

        /**
         *) on installment we submit also installment period.
         *) Note: on installment for germany we also submit holder, iban, bic, country,
         *)       but we don't need to cache request by these fields that`s why it`s not included here.
         *
         * @see Payolution_Client_Request_PreAuth::paymentIntoTransaction()
         */
        if ($paymentMethod == PaymentMethod::Installment()) {
            /* @var $form InstallmentForm */
            $form = $payment->paymentOptionsForm;
            $period = $form ? $form->installmentPeriod()->value() : null;
        } else {
            $period = null;
        }

        $hash = $this->hash('precheck', [
            $paymentMethod,
            $customer,
            $paymentXml->asXML(),
            $basketItems,
            $period,
        ]);

        $cached = $this->getCached($hash);

        return $cached ? $cached : $this->cache($hash,
            $this->api->precheck($paymentMethod, $customer, $payment,
                $basketItems)
        );
    }

    /**
     * @param string $precheckId
     * @param PaymentMethod $paymentMethod
     * @param CustomerType $customer
     * @param PaymentType $payment
     * @param ItemType[] $basketItems
     *
     * @return mixed|null|Response
     */
    public function preauth($precheckId, PaymentMethod $paymentMethod, CustomerType $customer, PaymentType $payment, $basketItems)
    {
        /**
         * Preauth request must invalidate a calculate() method cache to confirm
         * that "calculation-id" will be unique for any further `preauth`
         */
        $this->invalidateCalculateCallCache(PriceType::create($payment->amount), $customer->address->country);

        $hash = $this->hash('preauth', [
            $precheckId,
            $paymentMethod,
            $customer,
            $payment,
            $basketItems,
        ]);

        $cached = $this->getCached($hash);

        return $cached ? $cached : $this->cache($hash,
            $this->api->preauth($precheckId, $paymentMethod, $customer, $payment,
                $basketItems)
        );
    }

    /**
     * @param $preauthResponseId
     * @param PaymentMethod $paymentMethod
     * @param PaymentType $payment
     * @param $basketItems
     *
     * @return mixed
     */
    public function update($preauthResponseId, PaymentMethod $paymentMethod, PaymentType $payment, $basketItems)
    {
        return $this->api->update($preauthResponseId, $paymentMethod, $payment,
            $basketItems);
    }

    /**
     * @param string $preauthResponseId
     * @param PaymentType $payment
     *
     * @return Response
     */
    public function capture($preauthResponseId, PaymentType $payment)
    {
        return $this->api->capture($preauthResponseId, $payment);
    }

    /**
     * @param string $preauthResponseId
     * @param PaymentType $payment
     *
     * @return Response
     */
    public function refund($preauthResponseId, PaymentType $payment)
    {
        return $this->api->refund($preauthResponseId, $payment);
    }

    /**
     * @param string                          $preauthResponseId
     * @param PaymentType $payment
     *
     * @return Response
     */
    public function reverse($preauthResponseId, PaymentType $payment)
    {
        return $this->api->reverse($preauthResponseId, $payment);
    }

    /**
     * @param PriceType $price
     * @param string $country
     *
     * @return Response
     */
    public function calculate(PriceType $price, $country)
    {
        $hash = $this->getHashKeyForCalculateCall($price, $country);
        $cached = null;

        return $cached ? $cached : $this->cache($hash,
            $this->api->calculate($price, $country)
        );
    }

    /**
     * Method returns a hash of all object which is stored in given array
     *
     * @param string $type
     * @param array $array
     *
     * @return string
     */
    private function hash($type, $array)
    {
        $c = '';
        foreach ($array as $item) {
            $c .= serialize($item);
        }

        return md5($c) . "/{$type}";
    }

    /**
     * Method returns cached response if it was previously cached.
     * hash you can generate with `hash` method.
     * Returns null if cached object was not found.
     *
     * @param string $hash
     *
     * @return mixed|null
     */
    private function getCached($hash)
    {
        // TODO: here we need to implement cache expiration by using cacheMap[$hash]['timestamp']
        return isset($this->cacheMap[$hash]) ? $this->cacheMap[$hash]['object'] : null;
    }

    /**
     * Method caches object within given hash. returns the same object which
     * was specified.
     *
     * @param string $hash
     * @param mixed  $object
     *
     * @return Response
     */
    private function cache($hash, Response $object)
    {
        $isConnectionError = !$object->success() && $object->error() &&
            ($object->error()->status == ErrorResponse::STATUS_CONNECTION_ERROR);

        // *) cache all except connection error.
        if (!$isConnectionError) {
            $this->cacheMap[$hash] = [
                'object'    => $object,
                'timestamp' => time()
            ];

            Registry::getSession()->setVariable($this->PERSISTENCE_SESSION_NAME, $this->cacheMap);
        }

        return $object;
    }

    /**
     * Method invalidates a cache if it exists for a given hash string.
     * If cache is empty then nothing happens.
     *
     * @param string $hash
     */
    private function invalidateCache($hash)
    {
        if (isset($this->cacheMap[$hash])) {
            unset($this->cacheMap[$hash]);
            Registry::getSession()->setVariable($this->PERSISTENCE_SESSION_NAME, $this->cacheMap);
        }
    }

    /**
     * @param PriceType $price
     * @param $country
     */
    private function invalidateCalculateCallCache(PriceType $price, $country)
    {
        $this->invalidateCache($this->getHashKeyForCalculateCall($price, $country));
    }

    /**
     * @param $price
     * @param $country
     *
     * @return string
     */
    private function getHashKeyForCalculateCall(PriceType $price, $country)
    {
        $price = $price->getPrice();

        if (is_string($price)) {
            $price = str_replace(',', '.', $price);
        } else if (!is_numeric($price)) {
            $price = 0.00;
        }

        return $this->hash('calculate', array(sprintf("%.2f", $price), strtoupper($country)));
    }

    /**
     * Method invalidates all API response cache
     */
    public function invalidateAllCache()
    {
        $this->cacheMap = null;
        Registry::getSession()->setVariable($this->PERSISTENCE_SESSION_NAME, $this->cacheMap);
    }
}
