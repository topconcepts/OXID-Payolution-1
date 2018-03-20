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
namespace TopConcepts\Payolution\Module\Controller;

use OxidEsales\Eshop\Application\Model\Payment;
use OxidEsales\Eshop\Application\Model\User;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\Request;
use OxidEsales\Eshop\Core\Session;
use OxidEsales\Eshop\Core\UtilsServer;
use TopConcepts\Payolution\AccessPoint;
use TopConcepts\Payolution\Client\Response\CalculationResponse;
use TopConcepts\Payolution\Client\Type\PriceType;
use TopConcepts\Payolution\Exception\PayolutionException;
use TopConcepts\Payolution\PayolutionModule;

/**
 * Extends default OXID order controller logic.
 * Added logic for nextstep to handle errors
 *
 * Class OrderController
 * @see \OxidEsales\Eshop\Application\Controller\OrderController
 * @package TopConcepts\Payolution\Module\Controller
 */
class OrderController extends OrderController_Parent
{
    /**
     * Return special payolution error link or parent link
     *
     * @param integer $iSuccess status code
     * @return  string  $sNextStep  partial parameter url for next step
     */
    protected function _getNextStep($iSuccess)
    {
        if ($iSuccess instanceof PayolutionException) {
            /* @var $error PayolutionException */
            $error = $iSuccess;
            $errorCode = $error->getCode();
            $errorText = $error->responseError() ? $error->responseError()->serialize() : '';

            $session = Registry::getSession();
            $session->setVariable('payerror', $errorCode);

            $nextStep = "payment?payerror={$errorCode}" . ($errorText ? "&payerrortext=" . urlencode($errorText) : '');
        } else {
            $nextStep = parent::_getNextStep($iSuccess);
        }

        return $nextStep;
    }

    public function cl()
    {
        /** @var PayolutionModule $module */
        $module = oxNew(AccessPoint::class)->getModule();
        /** @var Request $request */
        $request = Registry::get(Request::class);
        /** @var PriceType $price */
        $price = oxNew(PriceType::class);
        $price->setPrice($request->getRequestParameter('price'));

        /** @var CalculationResponse $response */
        $response = $module->getInstallmentConditions($price);

        echo json_encode([
                'status'                    => 'ok',
                'durations'                 => $response->range(),
                'serverSideInstallmentInfo' => $response->installmentInfo(),
            ]);
        die();
    }

    /**
     * @return void
     */
    public function check_installment()
    {
        try {
            /** @var PayolutionModule $module */
            $module = oxNew(AccessPoint::class)->getModule();

            /* @var $session Session */
            $session = $this->getSession();

            /* @var $user User */
            $user = $this->getUser();

            $basket = $session->getBasket();

            $userIpAddress = Registry::get(UtilsServer::class)->getRemoteAddress();

            $payment = $this->createInstallmentPayment();

            $aDynvalue = Registry::get(Request::class)->getRequestParameter('values');

            if (!$aDynvalue) {
                throw new \RuntimeException("missing post parameter `values`. must be an array");
            }

            if (!is_array($aDynvalue)) {
                throw new \RuntimeException("given argument `values` is not an array.");
            }

            // *) remove all IBAN, BIC, ACCOUNT HOLDER related fields from precheck.
            $aDynvalue = array_diff_key($aDynvalue, [
                    'payolution_installment_iban' => true,
                    'payolution_installment_bic' => true,
                    'payolution_installment_account_holder' => true,
                ]);

            $orderingContext = $module->createOrderingContext($user, $userIpAddress, $basket, $payment, $aDynvalue);

            $module->checkInstallmentAvailability($orderingContext);

            echo json_encode(array('status'    => 'ok'));

        } catch (PayolutionException $e) {

            $responseError = ($e->getCode() == PayolutionException::PAYOLUTION_REMOTE_ERROR_RESPONSE) ? $e->responseError() : null;

            // Putting through htmlentities to convert non utf-8 characters
            $text = Registry::getLang()->translateString($e->translationKey());

            $message = '<div>' . $text . "\n".
                ($responseError ?
                    "<br/><br/><small>".
                    "{$responseError->messageCode} {$responseError->status} {$responseError->reason} {$responseError->message}".
                    "</small>" :
                    ""
                ).
            '</div>';

            echo json_encode(array('status' => 'error', 'html' => $message));

        } catch (\Exception $e) {
            echo json_encode(array('status' => 'error', 'message' => $e->getMessage()));
        }
        die();
    }

    /**
     * @return Payment
     */
    private function createInstallmentPayment()
    {
        $payment = oxNew(Payment::class);
        $payment->load('payolution_installment');

        return $payment;
    }
}
