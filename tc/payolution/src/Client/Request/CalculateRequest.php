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

use OxidEsales\Eshop\Core\Registry;
use TopConcepts\Payolution\Client\Response\CalculationResponse;
use TopConcepts\Payolution\Client\Type\CalculationPaymentType;
use TopConcepts\Payolution\Client\Type\PaymentType;
use TopConcepts\Payolution\Client\Type\PriceType;

/**
 * Class CalculateRequest
 * @package TopConcepts\Payolution\Client\Request
 */
class CalculateRequest extends AbstractRequest
{
    /**
     * @param PriceType $price
     * @param string $country
     */
    public function __construct(PriceType $price, $country)
    {
        $transaction = $this->transaction();
        /** @var CalculationPaymentType $payment */
        $payment = oxNew(CalculationPaymentType::class);
        $payment->amount = $price->getPrice();

        $config = Registry::getConfig();
        $activeCurrencyId = $config->getShopCurrency();
        $currencyArray = $config->getCurrencyArray();
        $basket = Registry::getSession()->getBasket();
        $payment->currency = $basket ? $basket->getBasketCurrency()->name : $currencyArray[$activeCurrencyId]->name;
        $payment->usage = 'Order with ID ' . $transaction->identification->transactionId;
        $payment->code = PaymentType::CALCULATE;

        $transaction->payment = $payment;
        $transaction->analysis->targetCountry->code = $country;
    }

    /**
     * @param \SimpleXMLElement $xml
     *
     * @return CalculationResponse
     */
    public function convertResponse($xml)
    {
        return oxNew(CalculationResponse::class)->parse($xml);
    }
}
