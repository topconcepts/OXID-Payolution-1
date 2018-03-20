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
namespace TopConcepts\Payolution\Exception;

use OxidEsales\Eshop\Core\Exception\StandardException;
use TopConcepts\Payolution\Client\Response\ErrorResponse;

/**
 * Class PayolutionException
 * @package TopConcepts\Payolution\Exception
 */
class PayolutionException extends StandardException
{
    /**
     *   Caution:  these constants are hardcoded inside templates, so if you
     *   change these dont forget to change template.
     *
     * @file \web\modules\TopConcepts\Payolution\views\blocks\page\checkout\checkout_payment_errors.tpl
     *
     */

    /**
     * Installment has been rejected during PreCheck procedure,
     * This is thrown then we got return code '800.100.170' from server
     *
     * from Docs:
     * code: 800.100.170
     * text: transaction declined (transaction not permitted)
     * reason: Request declined by the payolution risk engine (risk score, velocity, number of open transactions)
     */
    const REJECTED = -200;
    /**
     * Missing required fields error code
     */
    const VALIDATION_REQUIRED_FIELDS_IS_MISSING = -201;
    /**
     * Age restriction error code
     */
    const VALIDATION_AGE_RESTRICTION = -202;
    /**
     * Address error code
     */
    const VALIDATION_ADDRESS = -203;
    /**
     * Terms and conditions error code
     */
    const VALIDATION_TERMS_AND_CONDITIONS = -204;
    /**
     * Status transition error code
     */
    const ORDER_STATUS_TRANSITION_NOT_ALLOWED = -205;
    /**
     * Remote error code
     */
    const PAYOLUTION_REMOTE_ERROR_RESPONSE = -206;

    /**
     * @var array
     */
    private static $messages = [
      self::REJECTED                              => 'REJECTED',
      self::VALIDATION_REQUIRED_FIELDS_IS_MISSING => 'VALIDATION_REQUIRED_FIELDS_IS_MISSING',
      self::VALIDATION_AGE_RESTRICTION            => 'VALIDATION_AGE_RESTRICTION',
      self::VALIDATION_ADDRESS                    => 'VALIDATION_ADDRESS',
      self::VALIDATION_TERMS_AND_CONDITIONS       => 'VALIDATION_TERMS_AND_CONDITIONS',
      self::ORDER_STATUS_TRANSITION_NOT_ALLOWED   => 'ORDER_STATUS_TRANSITION_NOT_ALLOWED',
      self::PAYOLUTION_REMOTE_ERROR_RESPONSE      => 'PAYOLUTION_REMOTE_ERROR_RESPONSE',
    ];

    private static $translationMap = [
        self::REJECTED                                 => 'PAYOLUTION_ERROR_REQUEST_WAS_REJECTED_ON_PRECHECK',
        self::VALIDATION_REQUIRED_FIELDS_IS_MISSING    => 'PAYOLUTION_ERROR_MISSING_REQUIRED_FIELDS',
        self::VALIDATION_AGE_RESTRICTION               => 'PAYOLUTION_ERROR_AGE_RESTRICTION',
        self::VALIDATION_ADDRESS                       => 'PAYOLUTION_ERROR_ADDRESSES_IS_NOT_THE_SAME',
        self::VALIDATION_TERMS_AND_CONDITIONS          => 'PAYOLUTION_ERROR_TERMS_AND_CONDITIONS_HAS_NOT_AGREED',
        self::ORDER_STATUS_TRANSITION_NOT_ALLOWED      => 'PAYOLUTION_ERROR_ORDER_STATUS_TRANSITION_NOT_ALLOWED',
        self::PAYOLUTION_REMOTE_ERROR_RESPONSE         => 'PAYOLUTION_ERROR_REMOTE_ERROR_RESPONSE',
    ];

    /**
     * @var ErrorResponse
     */
    private $_responseError;

    /**
     * @param string $code
     * @param null   $responseError
     */
    public function __construct($code, $responseError = null)
    {
        parent::__construct(self::$messages[$code], $code);
        $this->_responseError = $responseError;
    }

    /**
     * @return string
     */
    public function translationKey()
    {
        $code = $this->getCode();
        return isset(self::$translationMap[$code]) ? self::$translationMap[$code] : self::$messages[$code];
    }

    /**
     * This property is available only then error code is
     * 'Payolution_Error::PAYOLUTION_REMOTE_ERROR_RESPONSE'
     *
     * @return null|ErrorResponse
     */
    public function responseError()
    {
        return $this->_responseError;
    }

    /**
     * @param $errorCode
     *
     * @throws PayolutionException
     */
    public static function throwError($errorCode)
    {
        /** @var PayolutionException $e */
        $e = oxNew(PayolutionException::class, $errorCode);

        throw $e;
    }

    /**
     * @param ErrorResponse $responseError
     *
     * @throws PayolutionException
     */
    public static function throwResponseError(ErrorResponse $responseError) {
        /* @var $e PayolutionException */
        if ($responseError->messageCode === '800.100.170') {
            $e = oxNew(PayolutionException::class, self::REJECTED, $responseError);
        } else {
            $e = oxNew(PayolutionException::class, self::PAYOLUTION_REMOTE_ERROR_RESPONSE, $responseError);
        }

        throw $e;
    }
}
