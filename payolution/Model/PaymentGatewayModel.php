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

use OxidEsales\Eshop\Application\Model\PaymentGateway;
use TopConcepts\Payolution\AccessPoint;
use TopConcepts\Payolution\Exception\PayolutionException;
use TopConcepts\Payolution\PayolutionModule;

/**
 * Extending OXID default oxPaymentGateway class to perform needed payment procedures and actions
 * (e.g. amount reservation)
 *
 * Class PaymentGatewayModel
 * @mixin PaymentGateway
 * @package TopConcepts\Payolution\Module\Model
 */
class PaymentGatewayModel extends PaymentGatewayModel_Parent
{
    /**
     * Executes Payolution payment, returns true on success.
     *
     * @param double $dAmount Goods amount
     * @param object &$oOrder User ordering object
     *
     * @return bool
     */
    public function executePayment($dAmount, & $oOrder)
    {
        $blSuccess = parent::executePayment($dAmount, $oOrder);
        $sPaymentMethod = $this->_oPaymentInfo->oxuserpayments__oxpaymentsid->value;

        //-------------------[ EVENT: POSTCONFIRMATION ]--------------------------
        if (strstr($sPaymentMethod, 'payolution_')) {
            try {
                /* @var $module PayolutionModule */
                $module = oxNew(AccessPoint::class)->getModule();
                $order = $module->asPayolutionOrder($oOrder);
                $module->events()->onPostConfirmation($order);
                $blSuccess = true;
            } catch (PayolutionException $error) {
                $this->_iLastErrorNo = $error;
                $blSuccess = false;
            }
        }

        return $blSuccess;
    }
}
