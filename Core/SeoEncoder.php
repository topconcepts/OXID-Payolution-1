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
namespace TopConcepts\Payolution\Module\Core;

use OxidEsales\Eshop\Core\SeoEncoder as OxidSeoEncoder;
use OxidEsales\Eshop\Core\Registry;

/**
 * Class SeoEncoder
 * @package TopConcepts\Payolution\Module\Model
 */
class SeoEncoder extends OxidSeoEncoder
{
    /**
     * @return void
     */
    public function generatePayoPaymentPdfUrl()
    {
        $id = md5('index.php?cl=PayolutionPdfDownload');
        $url = 'index.php?cl=PayolutionPdfDownload';
        $shopId = Registry::getConfig()->getShopId();

        // Save DE seo url
        $seoUrlDe = 'kreditvertragsentwurf';
        $langIdDE = 0;
        $this->_saveToDb('static', $id, $url, $seoUrlDe, $langIdDE, $shopId, true);
        // Save EN seo url
        $seoUrlEn = 'loanagreementsentdraft';
        $langIdEn = 1;
        $this->_saveToDb('static', $id, $url, $seoUrlEn, $langIdEn, $shopId, true);
    }
}
