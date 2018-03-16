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
namespace Payolution\Utils;
use OxidEsales\Eshop\Core\Registry;

/**
 * Class FormatterUtils
 * @package Payolution\Utils
 */
class FormatterUtils
{
    /**
     * @var string
     */
    private $date = 'd.m.Y';

    /**
     * @var string
     */
    private $time = 'H:i:s';

    /**
     * Format timestamp to date
     *
     * @param string $timestamp
     *
     * @return bool|string
     */
    public function date($timestamp)
    {
        return date($this->date, strtotime($timestamp));
    }

    /**
     * Format timestamp to time
     *
     * @param string $timestamp
     *
     * @return bool|string
     */
    public function time($timestamp)
    {
        return date($this->time, strtotime($timestamp));
    }

    /**
     * Format currency
     *
     * @param float $amount
     * @param string $sign
     *
     * @return string
     */
    public function currency($amount, $sign)
    {
        $currencySign = null;
        if (!is_object($sign)) {
            $currencies = Registry::getConfig()->getCurrencyArray();
            if (count($currencies)) {
                foreach ($currencies as $currency) {
                    if ($currency->sign == $sign) {
                        $currencySign = $currency;
                    }
                }
            }
        }
        $lang = Registry::getLang();

        return $lang->formatCurrency($amount, $currencySign);
    }
}
