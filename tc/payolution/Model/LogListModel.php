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
namespace TopConcepts\Payolution\Module\Model;

use OxidEsales\Eshop\Core\Model\ListModel;
use OxidEsales\Eshop\Core\DatabaseProvider as Db;

/**
 * Class LogListModel
 * @package TopConcepts\Payolution\Module\Model
 */
class LogListModel extends ListModel
{
    /**
     * List Object class name
     *
     * @var string
     */
    protected $_sObjectsInListName = LogModel::class;

    /**
     * Loads logs stored in DB, filtered by user groups, returns array, filled with
     * objects, that keeps logs data.
     *
     * @param integer $iFrom  number from which start selecting
     * @param integer $iLimit Limit of records to fetch from DB(default 0)
     *
     * @return void
     */
    public function loadLogs($iFrom = 0, $iLimit = 10)
    {
        if ($iLimit) {
            $this->setSqlLimit($iFrom, $iLimit);
        }

        $sNewsViewName = getViewName( 'payolution_apilog2' );
        $oBaseObject = $this->getBaseObject();
        $sSelectFields = $oBaseObject->getSelectFields();

        $sSelect = "select $sSelectFields from $sNewsViewName ";

        $this->selectString($sSelect);
    }

    /**
     * Returns count of all entries.
     *
     * @return integer $iRecCnt
     */
    public function getCount()
    {
        $oDb = Db::getDb(Db::FETCH_MODE_ASSOC);

        $sNewsViewName = getViewName( 'payolution_apilog' );
        $sSelect = "select COUNT($sNewsViewName.`oxid`) from $sNewsViewName ";
        $iRecCnt = (int) $oDb->getOne( $sSelect );

        return $iRecCnt;
    }
}
