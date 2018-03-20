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
namespace TopConcepts\Payolution\Utils;

use OxidEsales\Eshop\Application\Model\Basket as OxidBasket;
use OxidEsales\Eshop\Application\Model\Country;
use OxidEsales\Eshop\Application\Model\User;
use TopConcepts\Payolution\Basket\Basket;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\Request;
use TopConcepts\Payolution\Basket\BasketItem;
use TopConcepts\Payolution\Basket\DummyBasket;
use TopConcepts\Payolution\Client\Type\Analysis\ItemType;
use TopConcepts\Payolution\Client\Type\Analysis\ShippingType;
use TopConcepts\Payolution\Client\Type\CustomerType;
use TopConcepts\Payolution\Client\Type\PaymentType;
use TopConcepts\Payolution\Form\B2BForm;
use TopConcepts\Payolution\Form\B2CForm;
use TopConcepts\Payolution\Form\InstallmentForm;
use TopConcepts\Payolution\Order\OrderContext;
use TopConcepts\Payolution\Payment\PaymentMethod;

/**
 * Class TransformUtils
 * @package TopConcepts\Payolution\Utils
 */
class TransformUtils
{
    const EMPTY_BIRTH_DATE = '0000-00-00';

    /**
     * @param OrderContext $context
     *
     * @return CustomerType
     */
    public function getCustomerTypeFromContext(OrderContext $context)
    {
        /* @var $customer CustomerType */
        $customer = oxNew(CustomerType::class);

        $user = $context->user();

        // *) personal info

        $customer->name->given  = $user->oxuser__oxfname->getRawValue();
        $customer->name->family = $user->oxuser__oxlname->getRawValue();
        $customer->name->sex    = $this->getUserSex($user);

        $isRegistered           = (bool)trim($user->oxuser__oxpassword->value);
        $customer->isRegistered = $isRegistered;
        $customer->registerDate = $isRegistered ? $user->oxuser__oxcreate->value : null;

        switch ($context->paymentMethod()) {
            case PaymentMethod::InvoiceB2b():
                /* @var $form B2BForm */
                $form = $context->paymentOptionsForm();
                $this->updateCompany($user, $form->name()->value());
                $customer->company->name = $form->name()->value();
                $customer->company->type = $form->type()->getSelectedValue();
                $customer->company->ownerBirthdate = $this->extractBirthDate($form);
                $customer->company->ownerFamily = $form->ownerFamily()->value();
                $customer->company->ownerGiven = $form->ownerGiven()->value();

//              *) #PAYO-69
//                we need to determine, whether user entered a UID or a TradeRegistryNumber,
//                we can easily recognize UIDs because for Austria they always start with "ATU",
//                for Germany with "DE", and for Switzerland there will be
//                only TradeRegistryNumbers, because there are no UIDs(Vat - Nos) in Switzerland .

                $uidOrTradeRegistryNumber = $form->ustId()->value() ?: $user->oxuser__oxustid->value;

                $isUid = preg_match('/^\s*(ATU|DE)/i', $uidOrTradeRegistryNumber);

                if ($isUid) {
                    $customer->company->uid = $uidOrTradeRegistryNumber;
                } else {
                    $customer->company->tradeRegistryNumber = $uidOrTradeRegistryNumber;
                }

                $customer->name->birthdate = $this->extractBirthDate($form);

                break;

            default:
                /* @var $form B2CForm */
                /* @var $form InstallmentForm */
                $form = $context->paymentOptionsForm();

                $customer->name->birthdate = $this->extractBirthDate($form);

                break;
        }

        $form = $context->paymentOptionsForm();

        if ($context->isNetherlands() && $context->isInvoice()) {
            $user->oxuser__oxfon = new Field($form->phone()->value());
        }

        if ($context->isInvoice() && $user->oxuser__oxbirthdate->value == self::EMPTY_BIRTH_DATE) {
            $user->oxuser__oxbirthdate = new Field($this->extractBirthDate($form));
        }

        $user->save();

        // *) customer address info
        $customer->address->city    = $user->oxuser__oxcity->getRawValue();
        $customer->address->country = $this->getUserCountryISO2($user);
        $customer->address->street  = $this->getUserStreet($user);
        $customer->address->zip     = $user->oxuser__oxzip->value;
        $customer->address->state   = $user->oxuser__oxstateid->value;

        // *) customer contact details
        $customer->contact->email  = $user->oxuser__oxusername->value;
        $customer->contact->phone  = $user->oxuser__oxfon->value;
        $customer->contact->mobile = $user->oxuser__oxmobfon->value;
        $customer->contact->ip     = $context->userIpAddress();

        // *) customer system specific details
        $customer->username = $user->oxuser__oxusername->value;

        $customer->customerLanguage = $context->userCountry();
        $customer->customerFrontendLanguage = Registry::getLang()->getLanguageAbbr(Registry::get(Request::class)->getRequestParameter('lang'));
        $customer->shippingAddress = $this->getShippingAddressFromContext($context);

        return $customer;
    }

    /**
     * @param B2BForm $form
     *
     * @return string
     */
    private function extractBirthDate($form)
    {
        $birthdate = $form->birthday()->value() ?: Registry::getSession()->getUser()->oxuser__oxbirthdate->value;
        if ($birthdate) {
            $birthdate = new \DateTime($birthdate);

            return $birthdate->format('Y-m-d');
        }

        return '';
    }

    /**
     * @param OrderContext $context
     *
     * @return PaymentType
     */
    public function getPaymentTypeFromContext(OrderContext $context)
    {
        /* @var $payment PaymentType */
        $payment = oxNew(PaymentType::class);

        $payment->amount   = $context->basket()->totalOrderPrice();
        $payment->currency = $context->basket()->currency();
        $payment->usage    = '';

        $payment->paymentOptionsForm = $context->paymentOptionsForm();
        $payment->paymentMethod      = $context->paymentMethod();

        return $payment;
    }

    /**
     * @param OrderContext $context
     *
     * @return ItemType[]|array
     */
    public function getBasketItemsFromContext(OrderContext $context)
    {
        $list = [];

        foreach ($context->basket()->items() as $basketItem) {
            /* @var $item ItemType */
            $item = oxNew(ItemType::class);
            $item->descr    = $basketItem->description;
            $item->price    = $basketItem->price;
            $item->tax      = $basketItem->taxAmount;
            $item->category = $basketItem->category;

            $list[] = $item;
        }

        return $list;
    }

    /**
     * @param OxidBasket $basket
     *
     * @return Basket
     */
    public function castOxidBasketToPayolutionOrderingBasket($basket)
    {
        $totalOrderPrice = $basket->getPrice()->getBruttoPrice();
        $currency        = $basket->getBasketCurrency()->name;
        $items           = $this->getOrderingBasketItemsFromOxidBasket($basket);

        return Basket::create($totalOrderPrice, $currency,
          $items);
    }

    /**
     * Method converts order to basket. Basket object is dummy and used only
     * for this module needs.
     *
     * @param oxOrder $order
     *
     * @return DummyBasket
     */
    public function convertOxidOrderToOxidBasket($order)
    {
        /* @var $basket DummyBasket */
        $basket = oxNew(DummyBasket::class);

        $basket->setBruttoPrice($order->oxorder__oxtotalordersum->value);
        $basket->setBasketCurrencyName($order->oxorder__oxcurrency->value);


        $orderItems = $order->getOrderArticles();

        foreach ($orderItems as $orderItem) /* @var $orderItem oxorderarticle */ {
            $description  = $orderItem->oxorderarticles__oxtitle->value;
            $price        = $orderItem->oxorderarticles__oxbrutprice->value;
            $tax          = $orderItem->oxorderarticles__oxvatprice->value;
            $article      = $orderItem->oxorderarticles__oxid->value;
            $amount       = $orderItem->oxorderarticles__oxamount->value;
            $pricePerItem = $orderItem->oxorderarticles__oxbprice->value;

            $orderArticle    = $orderItem->getArticle();
            $articleCategory = $orderArticle ? $orderArticle->getCategory() : null;

            $category = $articleCategory ? $articleCategory->oxcategories__oxtitle->value : null;

            $basketItem = BasketItem::create($description,
              $price, $pricePerItem, $tax, $category, $article, $amount);

            $basket->addPayolutionBasketItem($basketItem);
        }

        return $basket;
    }

    /**
     * @param OxidBasket $basket
     *
     * @return BasketItem[]|array
     */
    private function getOrderingBasketItemsFromOxidBasket($basket)
    {

        if ($basket instanceof DummyBasket) {
            return $basket->getPayolutionBasketItems();
        }

        $list = [];

        /* @var $basketItems BasketItem[] */
        $basketItems = $basket->getContents();

        foreach ($basketItems as $basketItem) {

            $article         = $basketItem->getArticle(true, null, true);
            $articleCategory = $article->getCategory();

            $description  = trim($article->oxarticles__oxtitle->value);
            $price        = $basketItem->getPrice()->getBruttoPrice();
            $tax          = $basketItem->getPrice()->getVatValue();
            $category     = $articleCategory ? $articleCategory->oxcategories__oxtitle->value : null;
            $articleId    = $article->oxarticles__oxid->value;
            $amount       = $basketItem->getAmount();
            $pricePerItem = $basketItem->getUnitPrice()->getBruttoPrice();

            $list[] = BasketItem::create($description,
              $price, $pricePerItem, $tax, $category, $articleId, $amount);
        }

        return $list;
    }

    /**
     * @param oxUser $user
     *
     * @return string|null
     */
    private function getUserCountryISO2($user)
    {
        $id = $user->oxuser__oxcountryid;

        $country = oxnew(Country::class);

        /* @var $country Country */

        return $id && $country->load($id) ? $country->oxcountry__oxisoalpha2->value : null;
    }

    /**
     * @param User $user
     *
     * @return string M|F
     */
    private function getUserSex($user)
    {
        $s = strtoupper($user->oxuser__oxsal->value);

        $map = ['MR' => 'M', 'MRS' => 'F'];

        return isset($map[$s]) ? $map[$s] : null;
    }

    /**
     * @param User $user
     *
     * @return string
     */
    private function getUserStreet($user)
    {
        $street   = trim($user->oxuser__oxstreet->getRawValue());
        $streetnr = trim($user->oxuser__oxstreetnr->getRawValue());

        return implode(' ', array_filter([$street, $streetnr]));
    }

    /**
     * @param OrderContext $context
     * @return ShippingType
     */
    private function getShippingAddressFromContext(OrderContext $context)
    {
        $user = $context->user();

        /* @var $shippingAddress ShippingType */
        $shippingAddress = oxNew(ShippingType::class);

        $oxAddress = $context->shippingAddress();

        $shippingAddress->given  = $oxAddress->oxaddress__oxfname->value;
        $shippingAddress->family = $oxAddress->oxaddress__oxlname->value;

        $shippingAddress->company = $oxAddress->oxaddress__oxcompany->value;

        $shippingAddress->city    = $oxAddress->oxaddress__oxcity->value;
        $shippingAddress->country = $this->getUserCountryISO2($user);
        $shippingAddress->street  = implode(
            ' ',
            array_filter([$oxAddress->oxaddress__oxstreet->value, $oxAddress->oxaddress__oxstreetnr->value])
        );
        $shippingAddress->zip     = $oxAddress->oxaddress__oxzip->value;
        $shippingAddress->state   = $oxAddress->oxaddress__oxstateid->value;

        return $shippingAddress;
    }

    /**
     * @param User $user
     * @param string $company
     */
    private function updateCompany($user, $company) 
    {
        $user->oxuser__oxcompany = new Field($company, Field::T_RAW);
        $user->save();
    }

} 
