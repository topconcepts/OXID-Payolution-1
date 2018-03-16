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
namespace Payolution\Validation;

use OxidEsales\Eshop\Core\Price;
use Payolution\Config\Configuration;
use Payolution\Exception\PayolutionException;
use Payolution\Form\B2BForm;
use Payolution\Form\B2CForm;
use Payolution\Form\InstallmentForm;
use Payolution\Manager\ConfigManager;
use Payolution\Order\OrderContext;
use Payolution\Payment\PaymentMethod;
use Payolution\Utils\ValidationUtils;

/**
 * Class Payolution_Validation_Service
 * @package Payolution\Validation
 */
class ServiceValidation
{
    /**
     * @var Configuration
     */
    private $config;

    /**
     * @var ValidationUtils
     */
    private $validation;

    /**
     * @param ConfigManager $configManager
     */
    public function __construct(ConfigManager $configManager)
    {
        $this->config = $configManager->getConfig();
        $this->validation = oxNew(ValidationUtils::class);
    }

    /**
     * Validate required information according to payment method
     *
     * @param OrderContext $context
     *
     * @throws PayolutionException
     */
    public function validate(OrderContext $context)
    {
        switch ($context->paymentMethod()) {
            case PaymentMethod::InvoiceB2c() :
                $this->validateB2c($context);
                break;

            case PaymentMethod::InvoiceB2b() :
                $this->validateB2b($context);
                break;

            case PaymentMethod::Installment() :
                $this->validateInstallment($context);
                break;
        }
    }

    /**
     * @param OrderContext $context
     * @return bool
     */
    public function isShippingAddressAllowed(OrderContext $context)
    {
        return $this->addressValid($context);
    }

    /**
     * Validate required information for invoice-b2c payment method
     *
     * @param OrderContext $context
     *
     * @throws PayolutionException
     */
    private function validateB2c(OrderContext $context)
    {
        /* @var $form B2CForm */
        $form = $context->paymentOptionsForm();

        if (!$this->validation->mandatoryFieldsB2c($form)) {
            $this->throwError(PayolutionException::VALIDATION_REQUIRED_FIELDS_IS_MISSING);
        }

        if (!$this->validation->ageRestriction($form->birthday()->value())) {
            $this->throwError(PayolutionException::VALIDATION_AGE_RESTRICTION);
        }

        if (!$this->addressValid($context)) {
            $this->throwError(PayolutionException::VALIDATION_ADDRESS);
        }
    }

    /**
     * Validate required information for invoice-b2b payment method
     *
     * @param OrderContext $context
     *
     * @throws PayolutionException
     */
    private function validateB2b(OrderContext $context)
    {
        /* @var $form B2BForm */
        $form = $context->paymentOptionsForm();

        // *) we dont require any validations all fields are optional.
    }

    /**
     * Validate required information for installment payment method
     *
     * @param OrderContext $context
     */
    private function validateInstallment(OrderContext $context)
    {
        /* @var $form InstallmentForm */
        $form = $context->paymentOptionsForm();

        if (!$this->validation->mandatoryFieldsInstallment($form)) {
            $this->throwError(PayolutionException::VALIDATION_REQUIRED_FIELDS_IS_MISSING);
        }

        if (!$this->validation->ageRestriction($form->birthday()->value())) {
            $this->throwError(PayolutionException::VALIDATION_AGE_RESTRICTION);
        }
    }

    /**
     * Validate user bulling and shipping addresses
     *
     * @param OrderContext $context
     *
     * @return bool
     */
    private function addressValid(OrderContext $context)
    {
        return $this->config->allowDifferentShipAddr() ||
               $this->validation->addrsMatch($context->shippingAddress(), $context->billingAddress());
    }

    /**
     * Throws PayolutionException exception with specified code.
     * Codes are available as constants in PayolutionException class.
     *
     * @param int $code
     *
     * @throws PayolutionException
     */
    private function throwError($code)
    {
        PayolutionException::throwError($code);
    }

    /**
     * Whether or not product price falls into
     *
     * @param Price $price
     *
     * @return bool
     */
    public function installmentAvailable($price)
    {
        return $this->installmentAvailableOld($price->getPrice());
    }

    /**
     * Whether or not product price falls into
     *
     * @param float $price
     *
     * @return bool
     */
    public function installmentAvailableOld($price)
    {
        return $this->config->getMinInstallment() <= $price && $this->config->getMaxInstallment() >= $price;
    }
}
