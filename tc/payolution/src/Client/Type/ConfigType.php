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
namespace TopConcepts\Payolution\Client\Type;

/**
 * Class ConfigType
 * @package TopConcepts\Payolution\Client\Type
 */
class ConfigType
{
    /**
     * Channel type for other payments
     */
    const TYPE_CHANNEL_GATEWAY = 'gateway';

    /**
     *
     * Channel-Invoice: 8a82941832d84c500132e875fc0c0648
     * Channel-Installment: 8a82941832d84c500132e875fc0c0648
     * Channel-B2B-Invoice: ff8080813dea2264013deb2e4d2a0192
     * Pre-Check Channel ID: ff8080813b227bf4013b3d661a7c0f86
     *
     *
     * THIS MUST BE USED FOR TESTING:
     *
     * CHANNEL TEST SYNC: ff80808138e15f1f0138faec90910a22
     */

    /**
     * @var string
     */
    public $channel = self::TYPE_CHANNEL_GATEWAY;

    /**
     * @var string
     */
    public $login = '8a8294182f965dd4012f9b5c54f7016d';

    /**
     * @var null|string
     */
    public $calculationLogin = 'test-installment';

    /**
     * @var string
     */
    public $sender = '8a8294182f965dd4012f9b5c54f50169';

    /**
     * @var string
     */
    public $pass = 'Eg89ttKk';

    /**
     * @var null|string
     */
    public $calculationPwd = '8ugJSZpn';

    /**
     * @var string
     */
    public $mode = 'CONNECTOR_TEST';

    /**
     * @var string
     */
    public $calculationMode = 'TEST';

    /**
     * @var string
     */
    public $xml_url = 'https://test-gateway.payolution.com/ctpe/api';

    /**
     * @var null|string
     */
    public $shopUrl = 'http://www.testshop.com/';

    /**
     * @var string
     */
    public $calculationUrl = 'https://test-payment.payolution.com/payolution-payment/rest/request/v2';

    /**
     * @var bool|null
     */
    public $test_mode = true;

    /**
     * @param array $configuration
     */
    public function __construct(array $configuration)
    {
        $this->channel = isset($configuration['channel']) ? $configuration['channel'] : null;
        $this->login   = isset($configuration['login']) ? $configuration['login'] : null;
        $this->pass    = isset($configuration['pass']) ? $configuration['pass'] : null;
        $this->sender  = isset($configuration['sender']) ? $configuration['sender'] : 'gateway';

        $this->calculationLogin = isset($configuration['calculationLogin']) ? $configuration['calculationLogin'] : null;
        $this->calculationPwd   = isset($configuration['calculationPwd']) ? $configuration['calculationPwd'] : null;

        $this->shopUrl = isset($configuration['shopUrl']) ? $configuration['shopUrl'] : null;

        $this->test_mode = isset($configuration['test_mode']) ? $configuration['test_mode'] : null;

        if ($this->test_mode) {
            $this->mode            = 'CONNECTOR_TEST';
            $this->calculationMode = 'TEST';
            $this->xml_url         = 'https://test-gateway.payolution.com/ctpe/api';
            $this->calculationUrl  = 'https://test-payment.payolution.com/payolution-payment/rest/request/v2';
        } else {
            $this->mode            = 'LIVE';
            $this->calculationMode = 'LIVE';
            $this->xml_url         = 'https://gateway.payolution.com/ctpe/api';
            $this->calculationUrl  = 'https://payment.payolution.com/payolution-payment/rest/request/v2';
        }
    }

    /**
     * @param string $type one of TYPE_CHANNEL_B2B, TYPE_CHANNEL_B2C,
     *                     TYPE_CHANNEL_INSTALLMENT, TYPE_CHANNEL_PRECHECK or
     *                     TYPE_CHANNEL_CL
     *
     * @return string
     */
    public function getChannel($type)
    {
        return $this->channel[$type];
    }
}
