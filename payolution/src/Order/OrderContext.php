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
namespace TopConcepts\Payolution\Order;

use OxidEsales\Eshop\Application\Model\Address;
use OxidEsales\Eshop\Application\Model\Basket as OxidBasket;
use OxidEsales\Eshop\Application\Model\Country;
use OxidEsales\Eshop\Core\Registry;
use TopConcepts\Payolution\Basket\Basket;
use OxidEsales\Eshop\Application\Model\Payment;
use OxidEsales\Eshop\Application\Model\User;
use TopConcepts\Payolution\Form\BaseFormAbstract;
use TopConcepts\Payolution\Form\BindHelper;
use TopConcepts\Payolution\Manager\FormManager;
use TopConcepts\Payolution\Payment\PaymentMethod;
use TopConcepts\Payolution\Utils\TransformUtils;

/**
 * Do not create instances from this class directly, use Payolution Module API
 * for that.
 * @see Payolution_Module::createOrderingContext()
 * @see Payolution_Ordering_Manager::createOrderingContext()
 *
 * Class OrderContext
 * @package TopConcepts\Payolution\Order
 */
class OrderContext
{
    /**
     * @var FormManager
     */
    private $formManager;

    /**
     * @var User
     */
    private $_user;

    /**
     * @var string
     */
    private $_userIpAddress;

    /**
     * @var OxidBasket
     */
    private $_oxidBasket;

    /**
     * @var Payment
     */
    private $_payment;

    /**
     * @var null
     */
    private $_paymentOptions;

    /**
     * @var BaseFormAbstract
     */
    private $_paymentOptionsForm;

    /**
     * @var Basket
     */
    private $_basket;

    /**
     * @var TransformUtils
     */
    private $transformUtils;

    /**
     * @var BindHelper
     */
    private $bindHelper;

    /**
     * @param FormManager $formManager
     * @param User $user
     * @param $userIpAddress
     * @param OxidBasket $oxidBasket
     * @param Payment $payment
     * @param null $paymentOptions
     */
    public function __construct(
      FormManager $formManager,
      $user,
      $userIpAddress,
      $oxidBasket = null,
      $payment = null,
      $paymentOptions = null
    ) {
        $this->formManager     = $formManager;
        $this->_user           = $user;
        $this->_userIpAddress  = $userIpAddress;
        $this->_oxidBasket     = $oxidBasket;
        $this->_payment        = $payment;
        $this->_paymentOptions = $paymentOptions;


        $this->transformUtils = oxNew(TransformUtils::class);
        $this->bindHelper = oxNew(BindHelper::class);
    }

    /**
     * @return Basket
     */
    public function basket()
    {
        if (!$this->_basket) {
            $this->_basket = $this->transformUtils->castOxidBasketToPayolutionOrderingBasket($this->_oxidBasket);
        }

        return $this->_basket;
    }

    /**
     * @return Payment
     */
    public function payment()
    {
        return $this->_payment;
    }

    /**
     * @return User
     */
    public function user()
    {
        return $this->_user;
    }

    /**
     * @return mixed
     */
    public function userIpAddress()
    {
        return $this->_userIpAddress;
    }

    /**
     * @return null|string   return two chars uppercase ISO2 of user's country
     */
    public function userCountry()
    {
        $countryId = $this->user()->getFieldData('oxcountryid');

        $country = oxNew(Country::class);

        return $country->load($countryId) ? strtoupper($country->getFieldData('oxisoalpha2')) : null;
    }

    /**
     * @return null|PaymentMethod
     */
    public function paymentMethod()
    {
        return PaymentMethod::fromString($this->_payment->getId());
    }

    /**
     * @return array
     */
    public function paymentOptions()
    {
        return $this->_paymentOptions;
    }

    /**
     * @return Address
     */
    public function billingAddress()
    {
        $user    = $this->user();
        $address = oxNew(Address::class);

        $address->oxaddress__oxcompany   = clone $user->oxuser__oxcompany;
        $address->oxaddress__oxfname     = clone $user->oxuser__oxfname;
        $address->oxaddress__oxlname     = clone $user->oxuser__oxlname;
        $address->oxaddress__oxstreet    = clone $user->oxuser__oxstreet;
        $address->oxaddress__oxstreetnr  = clone $user->oxuser__oxstreetnr;
        $address->oxaddress__oxaddinfo   = clone $user->oxuser__oxaddinfo;
        $address->oxaddress__oxcity      = clone $user->oxuser__oxcity;
        $address->oxaddress__oxcountryid = clone $user->oxuser__oxcountryid;
        $address->oxaddress__oxstateid   = clone $user->oxuser__oxstateid;
        $address->oxaddress__oxzip       = clone $user->oxuser__oxzip;
        $address->oxaddress__oxfon       = clone $user->oxuser__oxfon;
        $address->oxaddress__oxfax       = clone $user->oxuser__oxfax;
        $address->oxaddress__oxsal       = clone $user->oxuser__oxsal;

        return $address;
    }

    /**
     * @return Address
     */
    public function shippingAddress()
    {
        $addressId = $this->user()->getSelectedAddressId();

        /* @var $address Address */
        $address = oxNew(Address::class);

        return $addressId && $address->load($addressId) ? $address : $this->billingAddress();
    }

    /**
     * Method returns a form which was entered during payment process, the
     * object type is one of Payolution_Form_B2b, Payolution_Form_B2c,
     * Payolution_Form_Installment.
     *
     * @return BaseFormAbstract
     */
    public function paymentOptionsForm()
    {
        if (!$this->_paymentOptionsForm) {
            $bindParams = ['dynvalue' => $this->paymentOptions()];
            $this->_paymentOptionsForm = $this->formManager->getPaymentForm($this->paymentMethod()->name(), $this, $bindParams);
        }

        return $this->_paymentOptionsForm;
    }

    /**
     * @return BindHelper
     */
    public function bindHelper()
    {
        return $this->bindHelper;
    }

    /**
     * @return bool
     */
    public function isGermany()
    {
        return $this->userCountry() === 'DE';
    }

    /**
     * @return bool
     */
    public function isNetherlands()
    {
        return $this->userCountry() === 'NL';
    }

    /**
     * @return bool
     */
    public function isGreatBritain()
    {
        return $this->userCountry() === 'GB';
    }

    public function isCountryViable()
    {
        return $this->isGermany();
    }

    /**
     * Checks if is invoice
     *
     * @return bool
     */
    public function isInvoice()
    {
        return in_array(
            $this->paymentMethod(), [
                PaymentMethod::InvoiceB2c(),
                PaymentMethod::InvoiceB2b(),
                ]);
    }

    /**
     * Checks if user phone is set
     *
     * @return bool
     */
    public function isUserPhoneSet()
    {
        $bindHelper = $this->bindHelper();
        $user = $this->user();
        $userPhone = $bindHelper->getPhone($user);

        return !empty($userPhone);
    }

    /**
     * Check if payment error
     *
     * @return bool
     */
    public function isPaymentError()
    {
        $paymentError = Registry::getSession()->getVariable('payolutionPaymentError');

        return !empty($paymentError);
    }
}
