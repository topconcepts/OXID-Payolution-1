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
namespace TopConcepts\Payolution\Payment;

/**
 * oxid must have a records on `oxpayments` table with OXID key:
 *
 * 'payolution_installment'  for  PaymentMethod::Installment()
 * 'payolution_invoice_b2b'  for  PaymentMethod::InvoiceB2b()
 * 'payolution_invoice_b2c'  for  PaymentMethod::InvoiceB2c()
 *
 * Class PaymentMethod
 * @package TopConcepts\Payolution\Payment
 */
class PaymentMethod
{
    /**
     * @param $name
     *
     * @return PaymentMethod|null
     */
    public static function fromString($name)
    {
        switch ($name) {
            case self::InvoiceB2b()->name():
                return self::InvoiceB2b();

            case self::InvoiceB2c()->name():
                return self::InvoiceB2c();

            case self::Installment()->name():
                return self::Installment();

            case self::DD()->name():
                return self::DD();
        }
        return null;
    }

    /**
     * @return PaymentMethod
     */
    public static function Installment()
    {
        return self::_get('payolution_installment', false);
    }

    /**
     * @return PaymentMethod
     */
    public static function InvoiceB2b()
    {
        return self::_get('payolution_invoice_b2b', true);
    }

    /**
     * @return PaymentMethod
     */
    public static function InvoiceB2c()
    {
        return self::_get('payolution_invoice_b2c', false);
    }

    /**
     * @return PaymentMethod
     */
    public static function DD(){
        return self::_get('payolution_dd', false);
    }

    /**
     * @return array|PaymentMethod[]
     */
    public static function enum()
    {
        return array(
          self::Installment(),
          self::InvoiceB2b(),
          self::InvoiceB2c(),
          self::DD(),
        );
    }

    /**
     * @var array
     */
    private static $instances = array();

    /**
     * @param $name
     * @param $forCompany
     *
     * @return PaymentMethod
     */
    private static function _get($name, $forCompany)
    {
        if (!isset(self::$instances[$name])) {
            self::$instances[$name] = oxNew(self::class, $name, $forCompany);
        }

        return self::$instances[$name];
    }

    /**
     * @var string
     */
    private $_name;

    /**
     * @var bool
     */
    private $forCompany;

    /**
     * @param string $name
     * @param bool $forCompany
     *
     * @deprecated do not use constructor directly! use static method instead!
     * @private
     */
    public function __construct($name, $forCompany)
    {
        $this->_name = $name;
        $this->forCompany = $forCompany;
    }

    /**
     * @return bool
     */
    public function isCompanyPaymentMethod()
    {
        return $this->forCompany;
    }

    /**
     * @return bool
     */
    public function isCustomerPaymentMethod()
    {
        return !$this->forCompany;
    }

    /**
     * @return string
     */
    public function name()
    {
        return $this->_name;
    }
}
