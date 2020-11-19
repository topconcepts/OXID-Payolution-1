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
namespace TopConcepts\Payolution\Manager;

use OxidEsales\Eshop\Core\Exception\StandardException;
use OxidEsales\Eshop\Core\Registry;
use TopConcepts\Payolution\Form\B2BForm;
use TopConcepts\Payolution\Form\B2BNLForm;
use TopConcepts\Payolution\Form\B2CForm;
use TopConcepts\Payolution\Form\B2CNLForm;
use TopConcepts\Payolution\Form\BaseFormAbstract;
use TopConcepts\Payolution\Form\DirectDebitForm;
use TopConcepts\Payolution\Form\InstallmentDeForm;
use TopConcepts\Payolution\Form\InstallmentForm;
use TopConcepts\Payolution\Order\OrderContext;
use TopConcepts\Payolution\Payment\PaymentMethod;

/**
 * Class FormManager
 * @package TopConcepts\Payolution\Manager
 */
class FormManager
{
    /**
     * @var string
     */
    private $SESSION_VARIABLE = 'payo_form_cache';

    /**
     * List of fields that should not be cached in session
     *
     * @var array
     */
    private $notCachedFields = [
        'payolution_invoice_b2c' => [
            'payolution_b2c_phone',
        ],
        'payolution_invoice_b2b' => [
            'payolution_b2b_name',
        ],
    ];

    /**
     * @param string $paymentMethod
     * @param OrderContext $context
     * @param null $bindParams
     *
     * @return mixed
     */
    public function getPaymentForm($paymentMethod, OrderContext $context, $bindParams = null)
    {
        switch ($paymentMethod) {
            case PaymentMethod::InvoiceB2b()->name():
                return $this->getInvoiceB2bForm($context, $bindParams);
            case PaymentMethod::InvoiceB2c()->name():
                return $this->getInvoiceB2cForm($context, $bindParams);
            case PaymentMethod::Installment()->name():
                return $this->getInstallmentForm($context, $bindParams);
            case PaymentMethod::DD()->name():
                return $this->getDDForm($context, $bindParams);
            default:
                $msg = "invalid paymentMethod specified (paymentMethod={$paymentMethod})";
                $e = oxNew(StandardException::class, $msg);
                throw $e;
        }
    }

    /**
     * Method returns the same form as `getPaymentForm` method, but with pre
     * filled fields from cache. All form field values are overridden by
     * `bindParams` if given.
     *
     * @param string $paymentMethod
     * @param OrderContext $context
     * @param null $bindParams
     *
     * @return BaseFormAbstract
     */
    public function getPaymentFormCached($paymentMethod, OrderContext $context, $bindParams = null)
    {
        $cachedValues = $this->getCache($this->getCacheKey($context, $paymentMethod));
        $params = array_merge($cachedValues ? $cachedValues : [],
            $bindParams ? $bindParams : []);

        return $this->getPaymentForm($paymentMethod, $context, $params);
    }

    /**
     * Method will cache a given form into a session.
     *
     * @param BaseFormAbstract $form
     */
    public function cachePaymentForm(BaseFormAbstract $form)
    {
        $values = [];
        foreach ($form->getElements() as $element) {
            $location = array_filter(preg_split('/\[|\]\[|\]/', $element->name()));

            // *) pretty scary logic. we'll split a field name into chunks and then build
            // *) a real associative array the same as described in field name.
            // *) e.g. name: "dyvalue[A][B][C]" will be converted into nested arrays:
            // *) result: ['dynvalue' => ['A' => ['B' => ['C' => form_field_value]]]]
            $p =& $values;
            $lastField = array_pop($location);
            foreach ($location as $part) {
                if (!isset($p[$part])) {
                    $p[$part] = [];
                }
                $p =& $p[$part];
            }

            if($this->shouldCacheField($form, $lastField)){
                $p[$lastField] = $element->value();
            }
        }

        $this->setCache($this->getCacheKey($form->orderingContext()), $values);
    }

    /**
     * Check if should cache given field
     *
     * @param BaseFormAbstract $form
     * @param string $fieldName
     *
     * @return bool
     */
    private function shouldCacheField(BaseFormAbstract $form, $fieldName)
    {
        $paymentId = $this->getFormPaymentId($form);
        $notCachedFields = $this->notCachedFields;

        if (empty($paymentId) || empty($notCachedFields) || empty($notCachedFields[$paymentId])) {
            return true;
        }

        return !in_array($fieldName, $notCachedFields[$paymentId]);
    }

    /**
     * Get payment ID from Payolution form
     *
     * @param BaseFormAbstract $form
     *
     * @return string
     */
    private function getFormPaymentId(BaseFormAbstract $form)
    {
        $orderingContext = $form->orderingContext();
        $payment = $orderingContext->payment();

        return $payment ? $payment->getId() : '';
    }

    /**
     * @param string $key
     * @param mixed  $value
     */
    private function setCache($key, $value)
    {
        $session = Registry::getSession();

        $cache = $session->getVariable($this->SESSION_VARIABLE);
        $cache = $cache ? $cache : array();
        $cache[$key] = $value;

        $session->setVariable($this->SESSION_VARIABLE, $cache);
    }

    /**
     * @param string $key
     *
     * @return mixed|null
     */
    private function getCache($key)
    {
        $cache = Registry::getSession()->getVariable($this->SESSION_VARIABLE);
        $cache = $cache ? $cache : array();

        return isset($cache[$key]) ? $cache[$key] : null;
    }

    /**
     * @param OrderContext $context
     * @param $paymentMethodId
     *
     * @return string
     */
    private function getCacheKey(OrderContext $context, $paymentMethodId = null)
    {
        if (!$paymentMethodId) {
            $paymentMethodId = $context->paymentMethod()->name();
        }

        return $paymentMethodId;
    }

    /**
     * @param OrderContext $context
     * @param $bindParams
     *
     * @return B2CForm
     */
    private function getInvoiceB2cForm(OrderContext $context, $bindParams)
    {
        return $context->isNetherlands() ?
            oxNew(B2CNLForm::class, $context, $bindParams) :
            oxNew(B2CForm::class, $context, $bindParams);
    }

    /**
     * @param OrderContext $context
     * @param $bindParams
     *
     * @return B2BForm
     */
    private function getInvoiceB2bForm(OrderContext $context, $bindParams)
    {
        return $context->isNetherlands() ?
            oxNew(B2BNLForm::class, $context, $bindParams) :
            oxNew(B2BForm::class, $context, $bindParams);
    }

    /**
     * @param OrderContext $context
     * @param $bindParams
     *
     * @return InstallmentForm
     */
    private function getInstallmentForm(OrderContext $context, $bindParams)
    {
        return ($context->isCountryRequireIBAN()) ?
            oxNew(InstallmentDeForm::class, $context, $bindParams) :
            oxNew(InstallmentForm::class, $context, $bindParams);
    }

    /**
     * @param OrderContext $context
     * @param $bindParams
     *
     * @return InstallmentForm
     */
    private function getDDForm(OrderContext $context, $bindParams)
    {
        return oxNew(DirectDebitForm::class, $context, $bindParams);
    }
}
