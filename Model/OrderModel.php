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
namespace TopConcepts\Payolution\Module\Model;

use OxidEsales\Eshop\Application\Model\Basket;
use OxidEsales\Eshop\Application\Model\Country;
use OxidEsales\Eshop\Application\Model\Order;
use OxidEsales\Eshop\Application\Model\User;
use OxidEsales\Eshop\Core\Exception\StandardException;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\Request;
use OxidEsales\Eshop\Core\UtilsDate;
use OxidEsales\Eshop\Core\UtilsView;
use TopConcepts\Payolution\AccessPoint;
use TopConcepts\Payolution\Exception\PayolutionException;
use TopConcepts\Payolution\Order\PayolutionOrder;
use TopConcepts\Payolution\Payment\PaymentMethod;
use TopConcepts\Payolution\PayolutionModule;

/**
 * OXID default oxOrder class extension to provide additional logic required by Payolution module.
 * Extending class contains procedures and methods needed to successfully perform Payolution payment logic
 *
 * Class OrderModel
 * @mixin Order
 * @package TopConcepts\Payolution\Module\Model
 */
class OrderModel extends OrderModel_Parent
{
    /**
     * @param $oPdf
     */
    public function pdfFooter($oPdf)
    {
        /** @var PayolutionModule $module */
        $module = oxNew(AccessPoint::class)->getModule();
        $lang = Registry::getLang();
        $order = $module->asPayolutionOrder($this);

        if ($order) {
            $aSkipForPayments = [
                PaymentMethod::Installment(),
                PaymentMethod::DD(),
            ];

            if (!in_array($order->paymentMethod(), $aSkipForPayments)) {
                $oContext = $order->orderingContext();
                $blIsNetherlandsInvoice = ($oContext->isNetherlands() && $oContext->isInvoice());
                $paymentReferenceId = $order->paymentReferenceId() ? strtoupper($order->paymentReferenceId()) : '';
                $iPdfLanguage = $this->getPdfLanguageId();
                list($receiver, $iban, $bic) = $this->getBankDetails($order);
                $sBankDetails = "{$receiver}, IBAN: {$iban}, BIC: {$bic}.";
                $sTotalOrderSumTitle = $lang->translateString('PAYOLUTION_PDF_FOOTER_TOTAL_SUM', $iPdfLanguage);
                $sTotalOrderSumValue = $this->getFormattedTotalOrderSumWithCurrencyName();
                $sTotalOrderSum = $blIsNetherlandsInvoice ? $sTotalOrderSumTitle . $sTotalOrderSumValue : '';

                $this->outputLines([
                    $lang->translateString('PAYOLUTION_PDF_FOOTER_TRANSFER', $iPdfLanguage),
                    $lang->translateString('PAYOLUTION_PDF_FOOTER_RECEIVER', $iPdfLanguage) . $sBankDetails,
                    $sTotalOrderSum,
                    $lang->translateString('PAYOLUTION_PDF_FOOTER_INTENDED_USE', $iPdfLanguage)." {$paymentReferenceId}",
                    $lang->translateString('PAYOLUTION_PDF_FOOTER_THE_BILL_DUE', $iPdfLanguage),
                    $lang->translateString('PAYOLUTION_PDF_FOOTER_TOTAL_DEBT', $iPdfLanguage),
                    $lang->translateString('PAYOLUTION_PDF_FOOTER_TOTAL_DEBT_2', $iPdfLanguage)
                    ],
                    $oPdf
                );
            }
        }

        if (!$order) {
            parent::pdfFooter($oPdf);
        }
    }

    /**
     * Get formatted total order sum with currency name suffix
     *
     * @return string
     */
    public function getFormattedTotalOrderSumWithCurrencyName()
    {
        $sTotalOrderSum = $this->getFormattedTotalOrderSum();
        $sCurrencyName = $this->getCurrency()->name;

        return "{$sTotalOrderSum} {$sCurrencyName}";
    }

    /**
     * Get language Id to be used in PDF
     *
     * @return int
     */
    public function getPdfLanguageId()
    {
        return (int) Registry::get(Request::class)->getRequestParameter("pdflanguage");
    }

    /**
     * @param array $lines
     * @param $oPdf
     */
    private function outputLines($lines, $oPdf)
    {
        $offsetY = 270;
        $lineHeight = 5;

        $lineCount = count($lines);

        $pdfBlock = new \PdfBlock();

        $font = method_exists($pdfBlock, 'getFont') ? $pdfBlock->getFont() : 'Arial';
        $oPdf->setFont($font, '', 10 );

        $offsetY -= $lineCount * $lineHeight;

        foreach ($lines as $line) {
            $oPdf->text(15, $offsetY, $line);
            $offsetY += $lineHeight;
        }
    }



    /**
     * Performs standard order cancelation process
     *
     * @return null
     */
    public function cancelOrder()
    {
        /* @var $module PayolutionModule */
        $module = oxNew(AccessPoint::class)->getModule();

        if($order = $module->asPayolutionOrder($this)) {

            try {

                $module->adminEvents()->onCancelOrDelete($order);

                parent::cancelOrder();

            } catch (\Exception $e) {

                $this->showExceptionError($e);

            }

        } else {
            parent::cancelOrder();
        }
    }

    /**
     * Disable payolution order deletions
     *
     * @param string $sOxId Ordering ID (default null)
     *
     * @return bool
     */
    public function delete($sOxId = null)
    {
        /* @var $module PayolutionModule */
        $module = oxNew(AccessPoint::class)->getModule();

        if ($sOxId) {
            /* @var $order Order */
            $order = oxNew(Order::class);
            $order = $order->load($sOxId) ? $order : null;
        } else $order = $this;

        $payolutionOrder = $order ? $module->asPayolutionOrder($order) : null;

        $isDeletable = !$payolutionOrder || $payolutionOrder->deletable() || 1 /* NOW ALWAYS ORDER IS DELETABLE */;

        if($isDeletable && $payolutionOrder) {
            try {

                $module->adminEvents()->onCancelOrDelete($payolutionOrder);

            } catch (\Exception $e) {

                $this->showExceptionError($e);

                return false;

            }
        }

        return $isDeletable ? parent::delete($sOxId): false;
    }

    /**
     * @param Exception $e
     */
    private function showExceptionError(\Exception $e)
    {
        if ($e instanceof PayolutionException) {

            $errorMessage = Registry::getLang()->translateString($e->translationKey(), null, false);

            if ($e->responseError()) {
                $responseError = $e->responseError();
                $errorText     = $responseError->status . ' :: ' . $responseError->reason . ' :: ' . $responseError->message . ' (' . $responseError->messageCode . ')';
                $errorMessage .= '<br/><br/>' . $errorText;
            }

            Registry::get(UtilsView::class)->addErrorToDisplay(oxNew(StandardException::class, $errorMessage), false, true);
        } else {
            if ($e instanceof \Exception) {
                Registry::get(UtilsView::class)->addErrorToDisplay(oxNew(StandardException::class, $e->getMessage()), false, true);
            }
        }
    }

    /**
     * Method oxorder._updateOrderDate() is not supported in old version.
     * But this method _markVouchers is supported, so we use this method for 'onConfirmerOrderHasBeenCreated' event.
     *
     * This method is called then order is successfully paid.
     *
     * @param Basket $oBasket
     * @param User $oUser
     */
    protected function _markVouchers($oBasket, $oUser)
    {
        parent::_markVouchers($oBasket, $oUser);
        $sPaymentMethod = $this->oxorder__oxpaymenttype->value;

        //-------------------[ EVENT: CONFIRMED ORDER HAS BEEN CREATED ]--------------------------
        if (strstr($sPaymentMethod, 'payolution_')) {
            /* @var $module PayolutionModule */
            $module = oxNew(AccessPoint::class)->getModule();
            $order = $module->asPayolutionOrder($this);
            $module->events()->onConfirmedOrderHasBeenCreated($order);
        }
    }

    /**
     * Exporting standard invoice pdf
     *
     * @param object $oPdf pdf document object
     *
     * @return void
     */
    public function exportStandart($oPdf)
    {
        // preparing order curency info
        $myConfig = $this->getConfig();
        $oPdfBlock = new \PdfBlock();

        $this->_oCur = $myConfig->getCurrencyObject($this->oxorder__oxcurrency->value);
        if (!$this->_oCur) {
            $this->_oCur = $myConfig->getActShopCurrencyObject();
        }

        // loading active shop
        $oShop = $this->_getActShop();

        // shop information
        $oPdf->setFont( $oPdfBlock->getFont(), '', 6 );
        $oPdf->text( 15, 55, $oShop->oxshops__oxname->getRawValue().' - '.$oShop->oxshops__oxstreet->getRawValue().' - '.$oShop->oxshops__oxzip->value.' - '.$oShop->oxshops__oxcity->getRawValue() );

        // billing address
        $this->_setBillingAddressToPdf( $oPdf );

        // delivery address
        if ( $this->oxorder__oxdelsal->value ) {
            $this->_setDeliveryAddressToPdf( $oPdf );
        }

        // loading user
        $oUser = oxNew(User::class);
        $oUser->load($this->oxorder__oxuserid->value);

        // user info
        $sText = $this->translate('ORDER_OVERVIEW_PDF_FILLONPAYMENT');
        $oPdf->setFont($oPdfBlock->getFont(), '', 5);
        $oPdf->text(195 - $oPdf->getStringWidth( $sText ), 55, $sText);

        // customer number
        $sCustNr = $this->translate('ORDER_OVERVIEW_PDF_CUSTNR').' '.$oUser->oxuser__oxcustnr->value;
        $oPdf->setFont($oPdfBlock->getFont(), '', 7);
        $oPdf->text(195 - $oPdf->getStringWidth( $sCustNr ), 59, $sCustNr);

        // setting position if delivery address is used
        if ($this->oxorder__oxdelsal->value) {
            $iTop = 115;
        } else {
            $iTop = 91;
        }

        // shop city
        $sText = $oShop->oxshops__oxcity->getRawValue().', '.date( 'd.m.Y', strtotime($this->oxorder__oxbilldate->value));
        $oPdf->setFont($oPdfBlock->getFont(), '', 10);
        $oPdf->text(195 - $oPdf->getStringWidth($sText), $iTop + 8, $sText);

        // shop VAT number
        if ($oShop->oxshops__oxvatnumber->value) {
            $sText = $this->translate('ORDER_OVERVIEW_PDF_TAXIDNR').' '.$oShop->oxshops__oxvatnumber->value;
            $oPdf->text(195 - $oPdf->getStringWidth( $sText ), $iTop + 12, $sText);
            $iTop += 8;
        } else {
            $iTop += 4;
        }

        // invoice number
        $sText = $this->translate('ORDER_OVERVIEW_PDF_COUNTNR').' '.$this->oxorder__oxbillnr->value;
        $oPdf->text(195 - $oPdf->getStringWidth($sText), $iTop + 8, $sText);

        // marking if order is canceled
        if ($this->oxorder__oxstorno->value == 1) {
            $this->oxorder__oxordernr->setValue($this->oxorder__oxordernr->getRawValue() . '   '.$this->translate( 'ORDER_OVERVIEW_PDF_STORNO' ), Field::T_RAW);
        }

        // order number
        $oPdf->setFont($oPdfBlock->getFont(), '', 12);
        $oPdf->text(15, $iTop, $this->translate('ORDER_OVERVIEW_PDF_PURCHASENR').' '.$this->oxorder__oxordernr->value);

        // order date
        $oPdf->setFont($oPdfBlock->getFont(), '', 10);
        $aOrderDate = explode( ' ', $this->oxorder__oxorderdate->value );
        $sOrderDate = Registry::get(UtilsDate::class)->formatDBDate($aOrderDate[0]);
        $oPdf->text(15, $iTop + 8, $this->translate('ORDER_OVERVIEW_PDF_ORDERSFROM').$sOrderDate.$this->translate('ORDER_OVERVIEW_PDF_ORDERSAT').$oShop->oxshops__oxurl->value);
        $iTop += 16;

        // product info header
        $oPdf->setFont($oPdfBlock->getFont(), '', 8);
        $oPdf->text(15, $iTop, $this->translate( 'ORDER_OVERVIEW_PDF_AMOUNT'));
        $oPdf->text(30, $iTop, $this->translate( 'ORDER_OVERVIEW_PDF_ARTID'));
        $oPdf->text(45, $iTop, $this->translate( 'ORDER_OVERVIEW_PDF_DESC'));
        $oPdf->text(135, $iTop, $this->translate( 'ORDER_OVERVIEW_PDF_VAT'));
        $oPdf->text(148, $iTop, $this->translate( 'ORDER_OVERVIEW_PDF_UNITPRICE'));
        $sText = $this->translate('ORDER_OVERVIEW_PDF_ALLPRICE');
        $oPdf->text(195 - $oPdf->getStringWidth($sText), $iTop, $sText);

        // separator line
        $iTop += 2;
        $oPdf->line(15, $iTop, 195, $iTop);

        // #345
        $siteH = $iTop;
        $oPdf->setFont($oPdfBlock->getFont(), '', 10);

        // order articles
        $this->_setOrderArticlesToPdf($oPdf, $siteH, true);

        // generating pdf file
        $oArtSumm = new payolution_pdfarticlesummary($this, $oPdf);
        $iHeight = $oArtSumm->generate($siteH);
        if ($siteH + $iHeight > 258) {
            $this->pdfFooter($oPdf);
            $iTop = $this->pdfHeader($oPdf);
            $oArtSumm->ajustHeight($iTop - $siteH);
            $siteH = $iTop;
        }

        $oArtSumm->run($oPdf);
        $siteH += $iHeight + 8;

        $oPdf->text(15, $siteH, $this->translate('ORDER_OVERVIEW_PDF_GREETINGS'));

        $sPaymentType = (string)$this->oxorder__oxpaymenttype->value;
        $sDirectDebitPaymentId = PaymentMethod::DD()->name();

        if ($sPaymentType == $sDirectDebitPaymentId) {
            $siteH += 8;
            $oPdf->text(15, $siteH, $this->translate('ORDER_OVERVIEW_PAYOLUTION_PARTIAL_DELIVERY_DD'));
        }
    }

    /**
     * Get bank details for invoice pdf
     * @param PayolutionOrder $order
     * @return array
     */
    private function getBankDetails($order)
    {
        $country = oxNew(Country::class);
        $country->load($order->oxidOrder()->getFieldData('oxbillcountryid'));
        $country = $country->getFieldData('oxisoalpha2');

        $config = Registry::getConfig();

        $receiver = $config->getConfigParam('sBankReceiver_'.$country);
        $iban     = $config->getConfigParam('sBankIBAN_'.$country);
        $bic      = $config->getConfigParam('sBankBIC_'.$country);

        if ($receiver == null || $iban == null || $bic == null) {
            $receiver = $config->getConfigParam('sBankReceiver');
            $iban     = $config->getConfigParam('sBankIBAN');
            $bic      = $config->getConfigParam('sBankBIC');
        }

        return [$receiver, $iban, $bic];
    }

    /**
     * @return bool
     */
    public function isPayolutionOrder()
    {
        return strpos($this->oxorder__oxpaymenttype->value, 'payolution') !== false;
    }

    /**
     * @return int
     */
    public function getPaymentTerm()
    {
        if (null === $iPaymentTerm = $this->getConfig()->getConfigParam('iPaymentTerm')) {
            $iPaymentTerm = 7;
        }

        return $iPaymentTerm;
    }

    /**
     * @param Basket $basket
     * @param User $user
     * @param bool $recalculatingOrder
     * @return int
     */
    public function finalizeOrder(Basket $basket, $user, $recalculatingOrder = false)
    {
        $rez = parent::finalizeOrder($basket, $user, $recalculatingOrder);
        Registry::getSession()->deleteVariable('FraudSessionId');

        return $rez;
    }
}
