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

use OxidEsales\Eshop\Application\Model\PaymentList;
use OxidEsales\Eshop\Application\Model\User;
use TopConcepts\Payolution\AccessPoint;
use TopConcepts\Payolution\PayolutionModule;

/**
 * Extending default OXID oxPaymentList class to add available Payolution payments
 *
 * Class PaymentListModel
 * @mixin PaymentList
 * @package TopConcepts\Payolution\Module\Model
 */
class PaymentListModel extends PaymentListModel_Parent
{
    /**
     * Loads and returns list of user payments, checks if country and currency 
     * matches for Payolution payments, check if country is active
     *
     * @param string $sShipSetId user chosen delivery set
     * @param double $dPrice     basket product price excl. discount
     * @param User $oUser      session user object
     *
     * @return array
     */
    public function getPaymentList( $sShipSetId, $dPrice, $oUser = null )
    {
        $aList = parent::getPaymentList( $sShipSetId, $dPrice, $oUser );
        $aList = $this->_setAvailablePayolutionPayments($aList, $dPrice, $oUser);

        return $aList;
    }
    
    /**
     * Set available Payolution payments
     * 
     * @param array $aList
     * @param double $dPrice
     * @param User $oUser
     * @return array
     */
    protected function _setAvailablePayolutionPayments($aList, $dPrice, $oUser = null)
    {
        /** @var User $oUser */
        $oUser = !is_null($oUser) ? $oUser : $this->getUser();
        /** @var PayolutionModule $module */
        $module = oxNew(AccessPoint::class)->getModule();
        $list = $module->getPaymentMethodsForUser($oUser);

        foreach ($aList as $iKey => $oPayment) {
            $name = $oPayment->getId();
            // search for Payolution payments
            if (strstr($name, 'payolution_') && !in_array($name, $list)) {
                unset($aList[$iKey]);
            }
        }

        return $aList;
    }
}
