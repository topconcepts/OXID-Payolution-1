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
namespace Payolution\Module\Core;

use OxidEsales\Eshop\Application\Model\Content;
use OxidEsales\Eshop\Application\Model\Order;
use OxidEsales\Eshop\Core\Email as OxidEmail;
use OxidEsales\Eshop\Core\Registry;
use Payolution\AccessPoint;
use Payolution\PayolutionModule;

/**
 * Class Email
 * @see OxidEmail
 * @mixin OxidEmail
 * @package Payolution\Module\Core
 */
class Email extends Email_Parent
{
    /**
     * @var string
     */
    protected $payoPdfEmailTemplate = 'email/html/payolution_order_pdf_email.tpl';
    /**
     * @var string
     */
    protected $payoPdfEmailPlainTemplate = 'email/plain/payolution_order_pdf_email.tpl';

    /**
     * @var bool
     */
    protected $addPayolutionBccEmail = false;

    /**
     * @param Order $order
     *
     * @return mixed
     */
    public function sendPayolutionOrderPdf($order) {
        /** @var PayolutionModule $payoModule */
        $payoModule = oxNew(AccessPoint::class)->getModule();
        $config = Registry::getConfig();
        $bccEmailAddress = $payoModule->getConfig()->getInvoicePdfEmailAddess();

        if (!$this->ValidateAddress($bccEmailAddress)) {
            // no email address to send to. Skip this email
            return false;
        }

        $this->setOrderEmailLang($order);

        $oShop = $this->_getShop();
        $this->_setMailParams($oShop);

        // create messages
        $oSmarty = $this->_getSmarty();
        $this->setViewData("order", $order);

        /** @var Content $oContent */
        $oContentPdfEmailHtml = oxNew(Content::class);
        $oContentPdfEmailHtml->loadByIdent('payolutionPdfEmailHtml');
        $sSubject = $oContentPdfEmailHtml->oxcontents__oxtitle->value;

        $sSubject = str_replace('[#oxordernr]', $order->oxorder__oxordernr->value, $sSubject);
        $sSubject = str_replace('[#sShopUrl]', $config->getShopUrl(), $sSubject);
        $sSubject = str_replace('[#oxorderdate-date]', date('d.m.Y', strtotime($order->oxorder__oxorderdate->value)), $sSubject);
        $sSubject = str_replace('[#oxorderdate-time]', date('H:i', strtotime($order->oxorder__oxorderdate->value)), $sSubject);

        $sTrimmedBillName = trim($order->oxorder__oxbilllname->getRawValue());
        $sFilename = $order->oxorder__oxordernr->value . "_" . $sTrimmedBillName . ".pdf";
        $sFilename = str_replace(" ", "_", $sFilename);
        ob_start();
        $order->genPDF($sFilename, $config->getConfigParam("pdflanguage"));
        $sPDF = ob_get_contents();
        ob_end_clean();


        $fileLocation = $config->getConfigParam('sCompileDir') . $sFilename;
        file_put_contents($fileLocation, $sPDF);

        $this->addAttachment($fileLocation);

        // Process view data array through oxOutput processor
        $this->_processViewArray();

        $this->setBody($oSmarty->fetch($this->payoPdfEmailTemplate));
        $this->setAltBody($oSmarty->fetch($this->payoPdfEmailPlainTemplate));

        $this->setSubject($sSubject);

        $this->setRecipient($bccEmailAddress);
        $this->setReplyTo(
            $oShop->oxshops__oxorderemail->value,
            $oShop->oxshops__oxname->getRawValue()
        );

        $blSuccess = $this->send();

        $this->resetLang();

        return $blSuccess;
    }

    /**
     * @param $order
     */
    protected function setOrderEmailLang($order)
    {
        $this->iOldBaseLang = Registry::getLang()->getBaseLanguage();
        $this->iOldTplLang  = Registry::getLang()->getTplLanguage();
        $orderLang          = 0;
        if (!empty($order->oxorder__oxlang->value) && is_numeric($order->oxorder__oxlang->value)) {
            $orderLang = $order->oxorder__oxlang->value;
        }

        Registry::getLang()->setBaseLanguage($orderLang);
        Registry::getLang()->setTplLanguage($orderLang);
    }

    /**
     * Set old language used before email sending
     */
    protected function resetLang()
    {
        if (!is_null($this->iOldBaseLang)) {
            Registry::getLang()->setBaseLanguage($this->iOldBaseLang);
            $this->iOldBaseLang = null;
        }

        if (!is_null($this->iOldTplLang)) {
            Registry::getLang()->setTplLanguage($this->iOldTplLang);
            $this->iOldTplLang = null;
        }
    }

    /**
     * Add payolution bcc email if order is payolution and sends ordering mail to user.
     * Returns true on success.
     *
     * @param Order $order   Order object
     * @param string  $subject user defined subject [optional]
     *
     * @return bool
     */
    public function sendOrderEmailToUser($order, $subject = null)
    {
        if ($this->isPayolutionPayment($order)) {
            $this->addPayolutionBccEmail = true;
        }

        return parent::sendOrderEmailToUser($order, $subject);
    }

    /**
     * Sets mail recipient to recipients array
     *
     * @param string $sAddress recipient email address
     * @param string $sName    recipient name
     *
     * @return null
     */
    public function setRecipient( $sAddress = null, $sName = null )
    {
        if (true === $this->addPayolutionBccEmail) {
            $this->addPayolutionBccEmail();
            $this->addPayolutionBccEmail = false;
        }

        return parent::setRecipient($sAddress, $sName);
    }

    /**
     * Return True if order is payolution
     *
     * @param Order $order
     * @return bool
     */
    private function isPayolutionPayment($order)
    {
        /** @var PayolutionModule $payoModule */
        $payoModule = oxNew(AccessPoint::class)->getModule();

        return $payoModule->isPayolutionOrder($order);
    }

    /**
     * Add payolution bcc email address
     */
    private function addPayolutionBccEmail()
    {
        $payoModule = oxNew(AccessPoint::class)->getModule();
        $bccEmailAddress = $payoModule->getConfig()->getInvoicePdfEmailAddess();
        if ($this->ValidateAddress($bccEmailAddress)) {
            $this->AddBCC($bccEmailAddress);
        }
    }
}
