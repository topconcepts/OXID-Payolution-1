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
namespace Payolution\Module\Controller\Admin\ApiLog;

use OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController;
use Payolution\Module\Model\LogModel;

/**
 * Class DetailsController
 * @package Payolution\Module\Controllers\Admin\ApiLog
 */
class DetailsController extends AdminDetailsController
{
    /** @var string $_sThisTemplate */
    protected $_sThisTemplate = 'payolution_apilogdetails.tpl';

    /**
     * @return string
     */
    public function render()
    {
        parent::render();

        $soxId = $this->_aViewData["oxid"] = $this->getEditObjectId();

        $this->_aViewData['oLogEntry'] = oxNew(LogModel::class);
        $this->_aViewData['oLogEntry']->load($soxId);

        return $this->_sThisTemplate;
    }
}
