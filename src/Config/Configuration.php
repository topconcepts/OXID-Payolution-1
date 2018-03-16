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
namespace Payolution\Config;

/**
 * Class Configuration
 * @package Payolution\Client\Config
 */
class Configuration
{

    /**
     * Key for test server in configuration
     */
    const TEST_SERVER_ID = 0;

    /**
     * Kei for live server in configuration
     */
    const LIVE_SERVER_ID = 1;

    private $staticParameters = [
        'installmentJsLibraryUrl'     => 'https://payment.payolution.com/payolution-payment/infoport/installments/generatejs',
        'installmentJsLibraryUrlTest' => 'https://test-payment.payolution.com/payolution-payment/infoport/installments/generatejs',
    ];

    /**
     * Configuration storage
     *
     * @var array
     */
    private $dynamicParameters = [
        'iPayolutionServerMode'            => '',
        'aPayolutionInsBankDataRequired'   => [],
        'blPayolutionAllowOtherShipAddr'   => false,
        'sPayolutionInvoicePdfEmailAddess' => '',
        // - for live server

        'sPayolutionSender'                => '',
        'sPayolutionLogin'                 => '',
        'sPayolutionPassword'              => '',
        'sPayolutionChannelCL'             => '',
        'sPayolutionPasswordCL'            => '',
        // - for test server

        'sPayolutionSenderTest'            => '',
        'sPayolutionLoginTest'             => '',
        'sPayolutionPasswordTest'          => '',
        'sPayolutionChannelCLTest'         => '',
        'sPayolutionPasswordCLTest'        => '',
        // --------

        'fPayolutionMinInstallment'        => 0,
        'fPayolutionMaxInstallment'        => 0,
        'bPayolutionShowPriceOnDetails'    => false,
        'bPayolutionShowPriceOnCategory'   => false,
        'bPayolutionShowPriceOnHomePage'   => false,
        'bPayolutionShowPriceOnBasket'     => false,
        'blPayolutionEnableLogging'        => false,
        'sBase64EncodedCompanyName'        => '',

    ];

    /**
     * @var array
     */
    private $parameters;

    /**
     * @param array $parameters
     */
    public function __construct($parameters)
    {
        $this->dynamicParameters = array_merge($this->dynamicParameters, $parameters);

        $this->parameters = array_merge($this->dynamicParameters, $this->staticParameters);
    }

    /**
     * List of countries where bank information is required when installment
     * payment method is used
     *
     * @return array
     */
    public function getInsBankDataRequired()
    {
        return $this->parameters['aPayolutionInsBankDataRequired'];
    }


    /**
     * Whether or not buyer should be allowed to create order with different
     * shipping and billing addresses
     *
     * @return boolean
     */
    public function allowDifferentShipAddr()
    {
        return (bool)$this->parameters['blPayolutionAllowOtherShipAddr'];
    }

    /**
     * Payolution user sender ID
     *
     * @return string
     */
    public function getSender()
    {
        return $this->envSpecific('sPayolutionSender');
    }


    /**
     * Payolution user login
     *
     * @return string
     */
    public function getLogin()
    {
        return $this->envSpecific('sPayolutionLogin');
    }

    /**
     * Payolution user password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->envSpecific('sPayolutionPassword');
    }

    /**
     * Payolution server Channel-Invoice-B2C
     *
     * @return string
     */
    public function getChannelInvoiceB2C()
    {
        return $this->envSpecific('sPayolutionChannelInvoiceB2C');
    }

    /**
     * Payolution server Channel-Invoice-B2B
     *
     * @return string
     */
    public function getChannelInvoiceB2B()
    {
        return $this->envSpecific('sPayolutionChannelInvoiceB2B');
    }

    /**
     * Payolution server Channel-Installment
     *
     * @return string
     */
    public function getChannelInstallment()
    {
        return $this->envSpecific('sPayolutionChannelIns');
    }

    /**
     * Payolution server Channel-Pre-Check
     *
     * @return string
     */
    public function getChannelPreCheck()
    {
        return $this->envSpecific('sPayolutionChannelPreCheck');
    }

    /**
     * Payolution server Channel-CL
     *
     * @return string
     */
    public function getChannelCL()
    {
        return $this->envSpecific('sPayolutionChannelCL');
    }

    /**
     * Payolution server Channel-CL for GB
     *
     * @return string
     */
    public function getChannelCLGB()
    {
        return $this->envSpecific('sPayolutionChannelCLGB');
    }

    /**
     * Payolution server DD-CL
     *
     * @return string
     */
    public function getChannelDD(){
        return $this->envSpecific('sPayolutionChannelDD');
    }

    /**
     * Payolution server Password-CL
     *
     * @return string
     */
    public function getPasswordCL()
    {
        return $this->envSpecific('sPayolutionPasswordCL');
    }

    /**
     * Payolution server Password-CL for GB
     *
     * @return string
     */
    public function getPasswordCLGB()
    {
        return $this->envSpecific('sPayolutionPasswordCLGB');
    }

    /**
     * Returns an URL which you can retrieve a javascript library for installment slider.
     * You'll get a full URL to library with automatically appended '?id=channelCl' query param.
     *
     * @return string
     */
    public function getInstallmentJsLibraryUrl()
    {
        $url = $this->envSpecific('installmentJsLibraryUrl');

        return $url ? $url . '?id=' . $this->getChannelCL() : null;
    }

    /**
     * Email address where generated invoice PDF's will be sent as a copy
     *
     * @return string
     */
    public function getInvoicePdfEmailAddess()
    {
        return $this->parameters['sPayolutionInvoicePdfEmailAddess'];
    }


    /**
     * Is payolution logging enabled
     *
     * @return mixed
     */
    public function isLoggerEnabled()
    {
        return $this->parameters['blPayolutionEnableLogging'];
    }

    /**
     * Return base64 encoded company name to use in GET-Parameter for
     * Terms-URLs in front end
     *
     * @return mixed
     */
    public function getBase64EncodedCompanyName()
    {
        return $this->parameters['sBase64EncodedCompanyName'];
    }

    /**
     * Payolution minimum installment amount
     *
     * @return float
     */
    public function getMinInstallment()
    {
        return $this->parameters['fPayolutionMinInstallment'];
    }

    /**
     * Payolution maximum installment amount
     *
     * @return float
     */
    public function getMaxInstallment()
    {
        return $this->parameters['fPayolutionMaxInstallment'];
    }

    /**
     * Whether or not installment prices should be shown on details page
     *
     * @return bool
     */
    public function showInstallmentPriceOnDetailsPage()
    {
        return (bool)$this->parameters['bPayolutionShowPriceOnDetails'];
    }

    /**
     * Whether or not installment prices should be shown on category/list pages
     *
     * @return bool
     */
    public function showInstallmentPriceOnCategoryPage()
    {
        return (bool)$this->parameters['bPayolutionShowPriceOnCategory'];
    }

    /**
     * Whether or not installment prices should be shown on home page
     *
     * @return bool
     */
    public function showInstallmentPriceOnHomePage()
    {
        return (bool)$this->parameters['bPayolutionShowPriceOnHomePage'];
    }

    /**
     * Whether or not installment prices should be shown on basket page
     *
     * @return bool
     */
    public function showInstallmentPriceOnBasket()
    {
        return (bool)$this->parameters['bPayolutionShowPriceOnBasket'];
    }

    /**
     * Payolution server mode (test/live)
     *
     * @return string
     */
    public function getServerMode()
    {
        return $this->parameters['iPayolutionServerMode'];
    }

    /**
     * Whether or not test server usage is enabled
     *
     * @return bool
     */
    public function testServerEnabled()
    {
        return $this->getServerMode() == self::TEST_SERVER_ID;
    }

    /**
     * Whether or not live server usage is enabled
     *
     * @return bool
     */
    public function liveServerEnabled()
    {
        return $this->getServerMode() == self::LIVE_SERVER_ID;
    }

    /**
     * Get list of available configuration parameters
     *
     * @return array
     */
    public function getParamsList()
    {
        return array_keys($this->dynamicParameters);
    }

    /**
     * Method returns a parameter name specific to current api environment
     * (test or live)
     *
     * For Test environment we append a prefix "Test" for each parameter.
     *
     * @param $param
     *
     * @return string
     */
    private function envSpecific($param)
    {
        $paramName = $this->liveServerEnabled() ? $param : "{$param}Test";

        return $this->parameters[$paramName];
    }
}
