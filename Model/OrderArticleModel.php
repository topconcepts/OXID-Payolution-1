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
namespace Payolution\Module\Model;

use OxidEsales\Eshop\Application\Model\OrderArticle;
use OxidEsales\Eshop\Core\Registry;

/**
 * Class Payolution_oxPayment extends OXID default oxPayment class to add additional
 * parameters and logic required by Payolution specific payments.
 * Also contains logic for compatibility between OXID versions
 *
 * Class OrderArticleModel
 * @see OrderArticle
 * @mixin OrderArticle
 * @package Payolution\Module\Model
 */
class OrderArticleModel extends OrderArticleModel_Parent
{
    /**
     * Get Total brut price formated
     *
     * @param null $amount
     *
     * @return string
     */
    public function getTotalBrutPriceFormated($amount = null)
    {
        $oLang = Registry::getLang();
        $oOrder = $this->getOrder();
        $oCurrency = $this->getConfig()->getCurrencyObject( $oOrder->oxorder__oxcurrency->value );

        return $oLang->formatCurrency( isset($amount) ? bcmul($this->oxorderarticles__oxbprice->value, $amount, 2) : $this->oxorderarticles__oxbrutprice->value, $oCurrency );
    }
}
