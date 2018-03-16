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
namespace Payolution\Module\Controller\Admin\Order;

use OxidEsales\Eshop\Application\Controller\Admin\OrderOverview;
use OxidEsales\Eshop\Application\Model\Order;
use OxidEsales\Eshop\Application\Model\UserPayment;
use OxidEsales\Eshop\Core\Email;
use Payolution\AccessPoint;
use Payolution\PayolutionModule;

/**
 * Class OverviewController
 * @see OrderOverview
 * @package Payolution\Module\Controllers\Admin\Order
 */
class OverviewController extends OverviewController_Parent
{
    /**
     * @return void
     */
    public function createPDF()
    {
        $soxId = $this->getEditObjectId();
        if ($soxId != "-1" && isset($soxId)) {
            // load object
            /** @var Order $oOrder */
            $oOrder = oxNew(Order::class);
            /** @var PayolutionModule $module */
            $module = oxNew(AccessPoint::class)->getModule();
            if ($oOrder->load($soxId) && $oOrder->oxorder__payo_pdf_sent->value !== '1' && $module->isPayolutionOrder($oOrder)) {
                /** @var \Payolution\Module\Core\Email $emailService */
                $emailService = oxNew(Email::class);
                $blSuccess = $emailService->sendPayolutionOrderPdf($oOrder);
                if ($blSuccess) {
                    $oOrder->oxorder__payo_pdf_sent->value = 1;
                    $oOrder->save();
                }
            }
        }

        parent::createPDF();
    }

    /**
     * Returns user payment used for current order.
     * Translates payolution dynValue names on older oxid versions
     *
     * @param Order $oOrder  object
     *
     * @return UserPayment
     */
    protected function _getPaymentType($oOrder)
    {
        $oUserPayment = parent::_getPaymentType($oOrder);
        $this->prependReferenceId($oUserPayment, $oOrder);

        return $oUserPayment;
    }

    /**
     * If given user payment is a Payolution payment, add Payolution Reference ID to payment dynvalues.
     *
     * @param UserPayment $payment
     * @param Order $oOrder
     */
    private function prependReferenceId($payment, $oOrder)
    {
        if (strpos($payment->oxuserpayments__oxpaymentsid->getRawValue(), 'payolution') !== false) {
            $values = $payment->getDynValues();

            $reference = new \stdClass();
            $reference->name = 'payolution_reference_id';
            $reference->value = $oOrder->oxorder__payo_reference_id->getRawValue();

            array_unshift($values, $reference);
            $payment->setDynValues($values);
        }
    }
}
