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
namespace Payolution\Basket;

use OxidEsales\Eshop\Application\Model\Basket;

/**
 * Class Payolution_Ordering_DummyBasket is used only as dummy object for
 * helping out to convert oxid order object into oxid basket object which will
 * be used in OrderingContext creation.
 *
 * Class DummyBasket
 * @package Payolution\Basket
 */
class DummyBasket extends Basket
{
    /**
     * @var string
     */
    private $currencyName;

    /**
     * @var array|BasketItem[]
     */
    private $items = [];

    /**
     * @param double $priceAmount
     */
    public function setBruttoPrice($priceAmount)
    {
        $price = $this->getPrice();
        $price->setBruttoPriceMode();
        $price->setPrice($priceAmount);
    }

    /**
     * @param string $currencyName
     */
    public function setBasketCurrencyName($currencyName)
    {
        $this->currencyName = $currencyName;
    }

    /**
     * Basket currency getter
     *
     * @return object
     */
    public function getBasketCurrency()
    {
        return (object) ['name' => $this->currencyName];
    }

    /**
     * @param BasketItem $basketItem
     */
    public function addPayolutionBasketItem(BasketItem $basketItem)
    {
        $this->items[] = $basketItem;
    }

    /**
     * @return array|BasketItem[]
     */
    public function getPayolutionBasketItems()
    {
        return $this->items;
    }
}
