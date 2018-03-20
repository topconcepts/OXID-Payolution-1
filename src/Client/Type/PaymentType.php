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
namespace TopConcepts\Payolution\Client\Type;

use TopConcepts\Payolution\Client\SerializableInterface;
use TopConcepts\Payolution\Client\Utils;
use TopConcepts\Payolution\Form\BaseFormAbstract;
use TopConcepts\Payolution\Payment\PaymentMethod;

/**
 * Class PaymentType
 * @package TopConcepts\Payolution\Client\Type
 */
class PaymentType implements SerializableInterface, \Serializable
{
    /**
     * Pre-auth request code
     */
    const PREAUTH = 'VA.PA';

    /**
     * Capture request code
     */
    const CAPTURE = 'VA.CP';

    /**
     * Reversal request code
     */
    const REVERSAL = 'VA.RV';

    /**
     * Refund request code
     */
    const REFUND = 'VA.RF';

    /**
     * Calculate installment code
     */
    const CALCULATE = 'VA.CL';

    /**
     * (required) Defines the payment and operation type. For payolution payments the codes are:
     *
     *   VA.PA for Pre-Check, Pre- & Re-Authorization,
     *   VA.CP for Capture,
     *   VA.RV for Reversal and
     *   VA.RF for Refund'
     *
     * @var string
     */
    public $code;

    /**
     * (required) The currency (e.g. EUR, CHF).
     *
     * @var string
     */
    public $currency;


    /**
     * (required) The total amount of the transaction.
     *
     * @var float
     */
    public $amount;

    /**
     * (required) Human readable description of reason for the transaction.
     *
     * @var string
     */
    public $usage;

    /**
     * (required) selected payment method
     * This property is not serialized to this XML element `Payment`
     *
     * @var PaymentMethod
     */
    public $paymentMethod;

    /**
     * (required) payment options form which stores additional payment options
     * This property is not serialized to this XML element `Payment`, it`s serialized inside `Analysis` xml.
     *
     * @var BaseFormAbstract
     */
    public $paymentOptionsForm;

    /**
     * (required) payment invoice no from the system. it's order no of oxid order.
     * This property is not serializted to this XML element `Payment`.
     * @var string
     */
    public $invoiceId;

    /**
     * (required if Installment) this calcualtion ID is retrieved from CL request.
     * This property is not serializted to this XML element `Payment`.
     * @var string
     */
    public $calculationId;

    /**
     * @param \SimpleXMLElement $output
     */
    public function toXml(\SimpleXMLElement &$output)
    {
        if ($this->isNotEmpty()) {
            $payment =& $output->Payment;
            $payment['code'] = $this->code;
            $paymentPresentation =& $payment->Presentation;
            $paymentPresentation->Currency = $this->currency;
            $paymentPresentation->Usage    = $this->usage;
            $paymentPresentation->Amount   = $this->formattedAmount();
        }
    }

    /**
     * Format price amount (format 0.00)
     *
     * @return string
     */
    public function formattedAmount()
    {
        return Utils::formatMoney($this->amount);
    }

    /**
     * @return bool
     */
    public function isNotEmpty()
    {
        return $this->code || $this->currency || $this->usage || $this->amount;
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * String representation of object
     *
     * @link http://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     */
    public function serialize()
    {
        $options = [];
        if ($this->paymentOptionsForm) {
            foreach ($this->paymentOptionsForm->getElements() as $element)
                $options[$element->name()] = $element->value();
        }
        $obj                     = clone $this;
        $obj->paymentOptionsForm = $options;
        $obj->paymentMethod      = $this->paymentMethod ? $this->paymentMethod->name() : null;

        return json_encode($obj);
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Constructs the object
     *
     * @link http://php.net/manual/en/serializable.unserialize.php
     * @param string $serialized <p>
     *                           The string representation of the object.
     *                           </p>
     * @return void
     */
    public function unserialize($serialized)
    {
        $obj = json_decode($serialized);
        foreach ($obj as $key => $value) {
            switch ($key) {
                case 'paymentOptionsForm':
                    break;
                case 'paymentMethod':
                    $this->{$key} = $value ? Payolution_Payment_Method::fromString($value) : null;
                    break;
                default:
                    $this->{$key} = $value;
            }
        }
    }
}
