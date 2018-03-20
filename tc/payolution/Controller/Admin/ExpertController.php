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
namespace TopConcepts\Payolution\Module\Controller\Admin;

use OxidEsales\Eshop\Application\Controller\Admin\ShopConfiguration;

/**
 * For module configuration in OXID backend
 * Class ExpertController
 * @package TopConcepts\Payolution\Module\Controller\Admin
 */
class ExpertController extends ShopConfiguration
{
    /**
     * Current class template name.
     * @var string
     */
    protected $_sThisTemplate = 'payolution_expert.tpl';

    /**
     * Rendering Admin start page
     *
     * @return string
     */
    public function render()
    {
        // force shopid as parameter
        // Pass shop OXID so that shop object could be loaded
        $sShopOXID = $this->getConfig()->getShopId();
        $this->setEditObjectId($sShopOXID);

        parent::render();

        return $this->_sThisTemplate;
    }

}
