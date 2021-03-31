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
namespace TopConcepts\Payolution\Basket;

/**
 * Class Basket
 * @package TopConcepts\Payolution\Basket
 */
class Basket
{
    /**
     * @var double
     */
    private $_totalOrderPrice;

    /**
     * @var string
     */
    private $_currency;

    /**
     * @var BasketItem[]|array
     */
    private $_items;

    /**
     * @param double                           $totalOrderPrice
     * @param string                           $currency
     * @param BasketItem[] $items
     *
     * @return Basket
     */
    public static function create($totalOrderPrice, $currency, $items)
    {
        $instance = oxNew(self::class, $totalOrderPrice,
          $currency, $items);

        return $instance;
    }

    /**
     * @param $totalOrderPrice
     * @param $currency
     * @param $items
     */
    public function __construct($totalOrderPrice, $currency, $items)
    {
        $this->_totalOrderPrice = $totalOrderPrice;
        $this->_currency        = $currency;
        $this->_items           = $items;
    }

    /**
     * @return float|double
     */
    public function totalOrderPrice()
    {
        return $this->_totalOrderPrice;
    }

    /**
     * @return string
     */
    public function currency()
    {
        return $this->_currency;
    }

    /**
     * @return BasketItem[]|array
     */
    public function items()
    {
        return $this->_items;
    }
} 
