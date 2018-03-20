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
namespace TopConcepts\Payolution\Module\Controller;

use OxidEsales\Eshop\Application\Controller\FrontendController;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\Request;
use TopConcepts\Payolution\AccessPoint;
use TopConcepts\Payolution\Client\Response\CalculationResponse;
use TopConcepts\Payolution\Client\Type\PriceType;
use TopConcepts\Payolution\PayolutionModule;

/**
 * Class PdfDownloadController
 * @package TopConcepts\Payolution\Module\Controllers
 */
class PdfDownloadController extends FrontendController
{
    private $pdf = null;

    /**
     * Action to download payolution pdf
     */
    public function render()
    {
        /** @var Request $request */
        $request = Registry::get(Request::class);
        $duration = $request->getRequestParameter('duration');
        $priceValue = $this->getSession()->getBasket()->getBruttoSum();

        $this->downloadPdf($duration, $priceValue);
        $this->returnPdfToTheUser();
    }

    /**
     * Download pdf from payolution servers and save file contents to class property $this->pdf
     *
     * @param $duration
     * @param $priceValue
     */
    private function downloadPdf($duration, $priceValue)
    {
        /** @var PayolutionModule $module */
        $module = oxNew(AccessPoint::class)->getModule();

        $userName = $module->getConfig()->getChannelCL();
        $password = $module->getConfig()->getPasswordCL();

        $rRequest = curl_init($this->getPdfDownloadUrl($duration, $priceValue));
        curl_setopt($rRequest, CURLOPT_RETURNTRANSFER, false);
        curl_setopt($rRequest, CURLOPT_BINARYTRANSFER, true);
        curl_setopt($rRequest, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($rRequest, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($rRequest, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($rRequest, CURLOPT_USERPWD, $userName . ":" . $password);

        // Execute request and download file
        $response = curl_exec($rRequest);

        // Close connection and file pointer
        curl_close($rRequest);
        $this->pdf = $response;
    }

    /**
     * Return URL to download credit agreement pdf from payolution
     *
     * @param $duration
     * @param $priceValue
     * @return mixed
     */
    private function getPdfDownloadUrl($duration, $priceValue)
    {
        /** @var PayolutionModule $module */
        $module = oxNew(AccessPoint::class)->getModule();

        /** @var PriceType $price */
        $price = oxNew(PriceType::class);
        $price->setPrice($priceValue);

        /** @var CalculationResponse $response */
        $response = $module->getInstallmentConditions($price);
        $instalmentInfo = $response->installmentInfo();
        $pdfUrl = $instalmentInfo[$duration]['url'];

        return $pdfUrl;
    }

    /**
     * Output pdf saved in $this->pdf to the browser
     */
    private function returnPdfToTheUser()
    {
        $filename = 'Kreditvertragsentwurf.pdf';
        $oUtils = Registry::getUtils();
        $oUtils->setHeader("Pragma: public");
        $oUtils->setHeader("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        $oUtils->setHeader("Expires: 0");
        $oUtils->setHeader("Content-type: application/pdf");
        $oUtils->setHeader("Content-Disposition: attachment; filename=" . $filename);
        Registry::getUtils()->showMessageAndExit($this->pdf);
    }
}
