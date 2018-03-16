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

use OxidEsales\Eshop\Application\Model\Payment;
use OxidEsales\Eshop\Application\Model\User;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\UtilsServer;
use Payolution\AccessPoint;
use Payolution\Module\Core\Exception\PayolutionException;
use Payolution\PayolutionModule;

/**
 * Class Payolution_oxPayment extends OXID default oxPayment class to add additional
 * parameters and payment logic required by specific Payolution payments.
 *
 * Class PaymentModel
 * @mixin Payment
 * @package Payolution\Module\Model
 */
class PaymentModel extends PaymentModel_Parent
{

    /**
     * Function checks if loaded payment is valid to current basket
     *
     * @param array  $aDynvalue    dynamical value (in this case oxidcreditcard and oxiddebitnote are checked only)
     * @param string $sShopId      id of current shop
     * @param User $oUser        the current user
     * @param double $dBasketPrice the current basket price (oBasket->dprice)
     * @param string $sShipSetId   the current ship set
     *
     * @return bool true if payment is valid
     */
    public function isValidPayment($aDynvalue, $sShopId, $oUser, $dBasketPrice, $sShipSetId)
    {
        $blRet = parent::isValidPayment($aDynvalue, $sShopId, $oUser, $dBasketPrice, $sShipSetId);

        //-------------------[ EVENT: PRECONFIRMATION ]--------------------------
        if ($blRet) {
            /* @var $module PayolutionModule */
            $module = oxNew(AccessPoint::class)->getModule();
            $this->setPayolutionPaymentError('');
            if ($module->isPayolutionPayment($this)) {
                try {
                    $session = $this->getSession();
                    $basket = $session->getBasket();
                    $userIpAddress = Registry::get(UtilsServer::class)->getRemoteAddress();
                    $orderingContext = $module->createOrderingContext($oUser, $userIpAddress, $basket, $this, $aDynvalue);
                    $module->events()->onPreConfirmation($orderingContext);
                } catch (PayolutionException $e) {
                    $this->_iPaymentError = $e->getCode();

                    if ($e->responseError()) {
                        Registry::getSession()->setVariable('payerrortext', $e->responseError()->serialize());
                        $this->setPayolutionPaymentError($e->responseError()->serialize());
                    }

                    $blRet = false;
                }
            }
        }

        return $blRet;
    }

    /**
     * Set Payolution payment error message
     *
     * @param string $error
     */
    public function setPayolutionPaymentError($error)
    {
        Registry::getSession()->setVariable('payolutionPaymentError', (string)$error);
    }
}
