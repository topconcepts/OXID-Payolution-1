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
 * Class BasketItem
 * @package TopConcepts\Payolution\Basket
 */
class BasketItem
{
    /**
     * @var string
     */
    public $description;

    /**
     * @var float
     */
    public $price;

    /**
     * @var float
     */
    public $pricePerItem;

    /**
     * @var float
     */
    public $taxAmount;

    /**
     * @var string
     */
    public $category;

    /**
     * @var string
     */
    public $articleId;

    /**
     * @var float
     */
    public $amount;

    /**
     * @param             $description
     * @param             $price
     * @param             $pricePerItem
     * @param             $taxAmount
     * @param             $category (optional)
     * @param string|null $articleId
     * @param float|null  $amount
     *
     * @return BasketItem
     */
    public static function create(
      $description,
      $price,
      $pricePerItem,
      $taxAmount,
      $category = null,
      $articleId = null,
      $amount = null
    ) {
        /* @var $item BasketItem */
        $item = oxNew(self::class);

        $item->description  = $description;
        $item->price        = $price;
        $item->pricePerItem = $pricePerItem;
        $item->taxAmount    = $taxAmount;
        $item->category     = $category;
        $item->articleId    = $articleId;
        $item->amount       = $amount;

        return $item;
    }
}
