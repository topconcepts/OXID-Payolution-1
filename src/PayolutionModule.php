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
namespace Payolution;

use OxidEsales\Eshop\Application\Model\Basket;
use OxidEsales\Eshop\Application\Model\Country;
use OxidEsales\Eshop\Application\Model\Order;
use OxidEsales\Eshop\Application\Model\Payment;
use OxidEsales\Eshop\Application\Model\User;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\Request;
use TopConcepts\Payolution\Client\Response\CalculationResponse;
use TopConcepts\Payolution\Client\Response\ErrorResponse;
use TopConcepts\Payolution\Client\Type\PriceType;
use TopConcepts\Payolution\Config\Configuration;
use TopConcepts\Payolution\Exception\PayolutionException;
use TopConcepts\Payolution\Manager\FormManager;
use TopConcepts\Payolution\Order\OrderContext;
use TopConcepts\Payolution\Order\PayolutionOrder;
use TopConcepts\Payolution\Payment\PaymentMethod;
use TopConcepts\Payolution\Utils\FormatterUtils;
use TopConcepts\Payolution\Utils\UserUtils;
use TopConcepts\Payolution\Validation\ServiceValidation;

/**
 * Class Payolution_Module
 *
 * This class object is created by Payolution_AccessPoint only. Do not create
 * it directly.
 *
 * @see Payolution_AccessPoint::getModule()
 * Class PayolutionModule
 * @package Payolution
 */
class PayolutionModule
{
    /**
     * @var PayolutionServices
     */
    private $services;

    /**
     * @param PayolutionServices $services
     */
    public function __construct(PayolutionServices $services)
    {
        $this->services = $services;
    }

    /**
     * Get current Payolution version
     *
     * @return string
     */
    public function getModuleVersion()
    {
        $aModule = array('version' => '0.0.0');

        include dirname(__FILE__) . '/../../metadata.php';

        return $aModule['version'];
    }


    /**
     * @return Configuration
     */
    public function getConfig()
    {
        return $this->services->configManager()->getConfig();
    }

    /**
     * @return PayolutionEvents
     */
    public function events()
    {
        return $this->services->events();
    }

    /**
     * @return AdminEvents
     */
    public function adminEvents()
    {
        return $this->services->adminEvents();
    }

    /**
     * @return FormManager
     */
    public function forms()
    {
        return $this->services->forms();
    }

    /**
     * @return FormatterUtils
     */
    public function formatter()
    {
        return $this->services->formatter();
    }

    /**
     * @return ServiceValidation
     */
    public function validation()
    {
        return $this->services->validation();
    }

    /**
     * @param User $user
     *
     * @return PaymentMethod[]
     */
    public function getPaymentMethodsForUser($user)
    {
        $list = [];
        $b2b  = UserUtils::isB2b($user);

        foreach (PaymentMethod::enum() as $method) {
            if ($method->isCompanyPaymentMethod() == $b2b) {
                $list[] = $method->name();
            }
        }

        return $list;
    }

    /**
     * @param User $user
     * @param string $userIpAddress
     * @param Basket $basket
     * @param Payment $payment
     * @param array $paymentOptions
     *
     * @return OrderContext
     */
    public function createOrderingContext($user, $userIpAddress, $basket = null, $payment = null, $paymentOptions = null)
    {
        return $this->services->orderingManager()->createOrderingContext($user, $userIpAddress, $basket, $payment, $paymentOptions);
    }

    /**
     * @param Order $order
     *
     * @return null|PayolutionOrder
     */
    public function asPayolutionOrder($order)
    {
        return $this->services->orderingManager()->asPayolutionOrder($order);
    }

    /**
     * Method check whether specified payment is payolution payment
     *
     * @param Payment $oxidPayment
     *
     * @return bool
     */
    public function isPayolutionPayment(Payment $oxidPayment)
    {
        return $this->services->orderingManager()->asPaymentMethod($oxidPayment) ? true : false;
    }

    /**
     * Method check whether specified order is payolution order
     *
     * @param Order $oxidOrder
     *
     * @return bool
     */
    public function isPayolutionOrder($oxidOrder)
    {
        return $this->asPayolutionOrder($oxidOrder) ? true : false;
    }

    /**
     * Method deserializes serialized string and returns ReponseError object,
     * this is used for deserializing ResponseError object inside
     * checkout_payments_errors.tpl template.
     *
     * Method returns null if cannot deserialize given string.
     *
     * @param $serializedData
     *
     * @return ErrorResponse|null
     */
    public function deserializeResponseError($serializedData)
    {
        return ErrorResponse::deserialize($serializedData);
    }

    /**
     * Payolution: CL - installment conditions
     *
     * @param PriceType $price
     *
     * @return CalculationResponse
     */
    public function getInstallmentConditions(PriceType $price)
    {
        $country = oxNew(Country::class);
        $user = Registry::getSession()->getUser();

        if ($user) {
            $countryId = $user->getFieldData('oxcountryid');
            $country->load($countryId);
            $country = $country->getFieldData('oxisoalpha2');
        } else {
            $country = 'DE';
        }

        return $this->services->payolutionApi()->calculate($price, $country);
    }

    /**
     * @param OrderContext $context
     *
     * @throws PayolutionException
     */
    public function checkInstallmentAvailability(OrderContext $context)
    {
        $this->services->orderingManager()->precheckByContext($context);
    }

    /**
     * Check if current page is a home page
     *
     * @return bool
     */
    public function isHomePage()
    {
        $cl = Registry::get(Request::class)->getRequestParameter('cl');

        return ($cl === null || $cl === 'start');
    }

    /**
     * Check if current page is a payment page
     *
     * @return bool
     */
    public function isPaymentPage()
    {
        return Registry::get(Request::class)->getRequestParameter('cl') === 'payment';
    }

    /**
     * Check if current page is a details page
     *
     * @return bool
     */
    public function isDetailsPage()
    {
        return Registry::get(Request::class)->getRequestParameter('cl') === 'details';
    }

    /**
     * Check if current page is a details page
     *
     * @return bool
     */
    public function isCategoryPage()
    {
        return Registry::get(Request::class)->getRequestParameter('cl') === 'alist';
    }

    /**
     * Check if current page is a basket page
     *
     * @return bool
     */
    public function isBasketPage()
    {
        return Registry::get(Request::class)->getRequestParameter('cl') === 'basket';
    }

    /**
     * Whether or not installment modal template should be included
     *
     * @return bool
     */
    public function shouldIncludeModal()
    {
        return $this->isPaymentPage()
        || $this->isHomePage() && $this->getConfig()->showInstallmentPriceOnHomePage()
        || $this->isDetailsPage() && $this->getConfig()->showInstallmentPriceOnDetailsPage()
        || $this->isCategoryPage() && $this->getConfig()->showInstallmentPriceOnCategoryPage()
        || $this->isBasketPage() && $this->getConfig()->showInstallmentPriceOnBasket();
    }
}
