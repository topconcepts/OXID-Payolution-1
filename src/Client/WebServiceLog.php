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
namespace TopConcepts\Payolution\Client;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\UtilsDate;
use TopConcepts\Payolution\Logger\OrderLogger;
use TopConcepts\Payolution\Module\Model\LogModel;

/**
 * Class WebServiceLog
 * @package TopConcepts\Payolution\Client
 */
class WebServiceLog extends WebService
{
    /**
     * @var OrderLogger
     */
    private $orderingLogger;

    /**
     * @param OrderLogger $orderingLoggger
     */
    public function __construct(OrderLogger $orderingLoggger)
    {
        $this->orderingLogger = $orderingLoggger;
    }

    /**
     * @param string $url
     * @param array  $postParameters
     * @param array  $authentication
     *
     * @return string
     */
    public function post($url, $postParameters, $authentication = array())
    {
        // change request data encoding to UTF-8
        $requestParameters = $postParameters;
        if (Registry::getConfig()->getConfigParam('iUtfMode') == 0) {
            foreach ($requestParameters as $key => $value) {
                $requestParameters[$key] = @iconv('ISO-8859-15', 'UTF-8', $value);
            }
        }

        $response = parent::post($url, $requestParameters, $authentication);

        $log = LogModel::create();

        $requestXml = isset($postParameters['load']) ? $postParameters['load'] : $postParameters['payload'];

        $log->setRequest($this->prettyXml($requestXml));
        $log->setResponse($response);
        $log->setAddedAt(date('Y-m-d H:i:s', Registry::get(UtilsDate::class)->getTime()));
        if ($this->orderingLogger->_getOrderInProgress()) {
            $log->setOrderId($this->orderingLogger->_getOrderInProgress()
              ->getId());
            $log->setOrderNo($this->orderingLogger->_getOrderInProgress()
              ->invoiceNo());
        }
        $log->save();

        return $response;
    }

    /**
     * Method formats given XML into pretty readable format
     *
     * @param $xml
     *
     * @return string
     */
    private function prettyXml($xml)
    {
        $doc = new \DomDocument('1.0');

        if (Registry::getConfig()->getConfigParam('iUtfMode') == 0) {
            $xml = @iconv('ISO-8859-15', 'UTF-8', $xml);
        }

        $doc->loadXML($xml);
        $doc->preserveWhiteSpace = false;
        $doc->formatOutput = true;

        $prettyXml = $doc->saveXML();

        if (Registry::getConfig()->getConfigParam('iUtfMode') == 0) {
            $prettyXml = @iconv('UTF-8', 'ISO-8859-15', $prettyXml);
        }

        return $prettyXml;
    }
}
