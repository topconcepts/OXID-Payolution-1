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
namespace TopConcepts\Payolution\Module\Controller\Admin\ApiLog;

use OxidEsales\Eshop\Application\Controller\Admin\AdminListController;
use OxidEsales\Eshop\Core\Registry;
use TopConcepts\Payolution\Module\Model\LogListModel;
use TopConcepts\Payolution\Module\Model\LogModel;

/**
 * Class ListController
 * @package TopConcepts\Payolution\Module\Controllers\Admin\ApiLog
 */
class ListController extends AdminListController
{
    /**
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = 'payolution_apiloglist.tpl';

    /**
     * Name of chosen object class (default null).
     *
     * @var string
     */
    protected $_sListClass = LogModel::class;

    /**
     * Type of list.
     *
     * @var string
     */
    protected $_sListType = LogListModel::class;

    /**
     * Default SQL sorting parameter (default null).
     *
     * @var string
     */
    protected $_sDefSortField = "added_at";

    /**
     * @param $conditionList
     * @param $queryString
     * @return mixed
     */
    public function _prepareWhereQuery($conditionList, $queryString) {
        $ret = parent::_prepareWhereQuery($conditionList, $queryString);
        $ret .= ' and payo_logs.oxshopid = '. Registry::getConfig()->getActiveShop()->getId();

        return $ret;
    }
}
