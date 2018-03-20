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

use OxidEsales\Eshop\Application\Model\Order;
use OxidEsales\Eshop\Application\Model\Payment;
use OxidEsales\Eshop\Application\Model\UserPayment;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\Eshop\Core\Registry;
use TopConcepts\Payolution\Basket\BasketItem;
use TopConcepts\Payolution\Form\BaseFormAbstract;
use TopConcepts\Payolution\Manager\FormManager;
use TopConcepts\Payolution\Manager\OrderManager;
use TopConcepts\Payolution\Payment\PaymentMethod;
use TopConcepts\Payolution\Utils\TransformUtils;
use OxidEsales\Eshop\Core\DatabaseProvider as Db;

/**
 * Class Payolution_Ordering_PayolutionOrder
 * @package TopConcepts\Payolution\Order
 */
class PayolutionOrder
{
    /**
     * @var Order
     */
    private $_oxidOrder;

    /**
     * @var string
     */
    private $_paymentMethodId;

    /**
     * @var OrderManager
     */
    private $manager;

    /**
     * @var FormManager
     */
    private $formManager;

    /**
     * @var TransformUtils
     */
    private $transformUtils;

    /**
     * @var BasketItem[]
     */
    private $orderArticles;

    /**
     * @param Order $order
     * @param OrderManager $manager
     * @param FormManager $formManager
     */
    public function __construct($order, OrderManager $manager, FormManager $formManager)
    {
        $this->_oxidOrder  = $order;
        $this->manager     = $manager;
        $this->formManager = $formManager;

        $this->transformUtils = oxNew(TransformUtils::class);

        $this->init($order);
    }

    /**
     * @param Order $order
     *
     * @return void
     */
    private function init($order)
    {
        /* @var $userPayment UserPayment */
        $userPayment = $order->getPaymentType();
        $this->_paymentMethodId = $userPayment->oxuserpayments__oxpaymentsid->value;
    }

    /**
     * @param Order $order
     * @param OrderManager $manager
     * @param FormManager $formManager
     *
     * @return PayolutionOrder
     */
    public static function from($order, OrderManager $manager, FormManager $formManager)
    {
        return oxNew(PayolutionOrder::class, $order, $manager, $formManager);
    }

    /**
     * @return PaymentMethod
     */
    public function paymentMethod()
    {
        return PaymentMethod::fromString($this->_paymentMethodId);
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
        return $this->orderingContext()->paymentOptionsForm();
    }

    /**
     * @return Order
     */
    public function oxidOrder()
    {
        return $this->_oxidOrder;
    }

    /**
     * @var OrderContext
     */
    private $_orderingContext;

    /**
     * @return OrderContext
     */
    public function orderingContext()
    {
        if (!$this->_orderingContext) {
            $paymentType = $this->_oxidOrder->getPaymentType();

            /* @var $paymentType UserPayment */
            $payment = $this->getPayment();
            $paymentOptions = $this->array2assoc($paymentType->getDynValues());

            $basket = $this->transformUtils->convertOxidOrderToOxidBasket($this->_oxidOrder);

            $this->_orderingContext = $this->manager->createOrderingContext(
              $this->_oxidOrder->getUser(),
              $this->ipAddress(),
              $basket,
              $payment,
              $paymentOptions
            );
        }

        return $this->_orderingContext;
    }

    /**
     * @return OrderStatus
     */
    public function status()
    {
        return OrderStatus::fromString($this->_oxidOrder->oxorder__payo_status->value);
    }

    /**
     * @param OrderStatus $status
     */
    public function setStatus(OrderStatus $status)
    {
        $this->_oxidOrder->oxorder__payo_status = new Field($status->name(),
          Field::T_TEXT);
        $this->_oxidOrder->save();
    }

    /**
     * @return string
     */
    public function ipAddress()
    {
        $ip = "";

        // * if we have IP address of customer stored inside oxid order then use it. or get ip from payolution order.
        if ($this->_oxidOrder->oxorder__oxip && $this->_oxidOrder->oxorder__oxip->value) {
            $ip = (string) $this->_oxidOrder->oxorder__oxip->value;
        } else {
            $ip = (string) $this->_oxidOrder->oxorder__payo_ip->value;
        }

        return $ip;
    }

    /**
     * @return int
     */
    public function invoiceNo()
    {
        return (string) $this->_oxidOrder->oxorder__oxordernr->value;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->_oxidOrder->getId();
    }

    /**
     * @return bool
     */
    public function deletable()
    {
        return (string) $this->_oxidOrder->oxorder__payo_nodel->value ? false : true;
    }

    /**
     * @param string $ipAddress
     */
    public function setIpAddress($ipAddress)
    {
        $this->_oxidOrder->oxorder__payo_ip = new Field($ipAddress, Field::T_TEXT);
        $this->_oxidOrder->save();
    }

    /**
     * @param string $responseId
     */
    public function setPrecheckId($responseId)
    {
        $this->_oxidOrder->oxorder__payo_precheck_id = new Field($responseId, Field::T_TEXT);
        $this->_oxidOrder->save();
    }

    /**
     * @param string $responseId
     */
    public function setPreauthId($responseId)
    {
        $this->_oxidOrder->oxorder__payo_preauth_id = new Field($responseId, Field::T_TEXT);
        $this->_oxidOrder->save();
    }

    /**
     * @param string $responseId
     */
    public function setCaptureId($responseId)
    {
        $this->_oxidOrder->oxorder__payo_capture_id = new Field($responseId, Field::T_TEXT);
        $this->_oxidOrder->save();
    }

    /**
     * @param $paymentReference
     */
    public function setPaymentReferenceId($paymentReference)
    {
        $this->_oxidOrder->oxorder__payo_reference_id = new Field($paymentReference, Field::T_TEXT);
        $this->_oxidOrder->save();
    }

    /**
     *
     */
    public function setAsDeletable()
    {
        $this->_oxidOrder->oxorder__payo_nodel = new Field('0', Field::T_RAW);
        $this->_oxidOrder->save();
    }

    /**
     *
     */
    public function setAsNotDeletable()
    {
        $this->_oxidOrder->oxorder__payo_nodel = new Field('1', Field::T_RAW);
        $this->_oxidOrder->save();
    }

    /**
     * @return string
     */
    public function precheckId()
    {
        return (string) $this->_oxidOrder->oxorder__payo_precheck_id->value;
    }

    /**
     * @return string
     */
    public function preauthId()
    {
        return (string) $this->_oxidOrder->oxorder__payo_preauth_id->value;
    }

    /**
     * @return string
     */
    public function captureId()
    {
        return (string) $this->_oxidOrder->oxorder__payo_capture_id->value;
    }

    /**
     * @return string
     */
    public function paymentReferenceId()
    {
        return (string) $this->_oxidOrder->oxorder__payo_reference_id->value;
    }


    /**
     * @return Payment
     */
    private function getPayment()
    {
        $paymentType = $this->_oxidOrder->getPaymentType();
        /* @var $paymentType UserPayment */

        $payment = oxNew(Payment::class);
        $payment->load((string) $paymentType->oxuserpayments__oxpaymentsid->value);

        return $payment;
    }

    /**
     * Method check if specified order is Payolution installment order
     *
     * @return bool
     */
    public function isInstallmentOrder()
    {
        /* @var $userPayment UserPayment */
        $userPayment = $this->oxidOrder()->getPaymentType();

        $paymentId = $userPayment ? $userPayment->oxuserpayments__oxpaymentsid->value : '';

        return $paymentId == 'payolution_installment';
    }

    /**
     * Method check if specified order is Payolution invoice B2C order
     *
     * @return bool
     */
    public function isB2COrder()
    {
        /* @var $userPayment UserPayment */
        $userPayment = $this->oxidOrder()->getPaymentType();
        $paymentId   = $userPayment ? $userPayment->oxuserpayments__oxpaymentsid->value : '';

        return $paymentId == 'payolution_invoice_b2c';
    }

    /**
     * Method check if specified order is Payolution invoice B2B order
     *
     * @return bool
     */
    public function isB2BOrder()
    {
        /* @var $userPayment UserPayment */
        $userPayment = $this->oxidOrder()->getPaymentType();

        $paymentId = $userPayment ? $userPayment->oxuserpayments__oxpaymentsid->value : '';

        return $paymentId == 'payolution_invoice_b2b';
    }

    /**
     * Whether or not order is marked as fully shipped
     *
     * @return bool
     */
    public function isShipped()
    {
        return $this->status() == OrderStatus::Shipped();
    }

    /**
     * Whether or not order is marked as canceled
     *
     * @return bool
     */
    public function isCanceled()
    {
        return $this->status() == OrderStatus::Cancelled();
    }

    /**
     * Whether or not order is marked as created
     *
     * @return bool
     */
    public function isCreated()
    {
        return $this->status() == OrderStatus::Created();
    }

    /**
     * @param $array
     *
     * @return array
     */
    private function array2assoc($array)
    {
        $results = [];

        foreach ($array as $item) {
            $results[$item->name] = $item->value;
        }

        return $results;
    }

    /**
     * Set total captured price during shipment
     *
     * @param string $value
     */
    public function setTotalCapturedPrice($value)
    {
        $this->_oxidOrder->oxorder__payo_captured_price = new Field($value, Field::T_RAW);
        $this->_oxidOrder->save();
    }

    /**
     * Get total captured price during shipment
     *
     * @return string
     */
    public function totalCapturedPrice()
    {
        return $this->_oxidOrder->oxorder__payo_captured_price->value
            ? $this->_oxidOrder->oxorder__payo_captured_price->value
            : '0.00';
    }

    /**
     * Set total pre-authorized price
     *
     * @param string $value
     */
    public function setPreauthPrice($value)
    {
        $this->_oxidOrder->oxorder__payo_preauth_price = new Field($value, Field::T_RAW);
        $this->_oxidOrder->save();
    }


    /**
     * Get total captured price during shipment
     *
     * @return string
     */
    public function preauthPrice()
    {
        return $this->_oxidOrder->oxorder__payo_preauth_price->value ? $this->_oxidOrder->oxorder__payo_preauth_price->value : '0.00';
    }


    /**
     * Set available refund amount
     *
     * @return void
     */
    public function setAvailableRefundAmount($value)
    {
        $this->_oxidOrder->oxorder__payo_refund_available = new Field($value, Field::T_RAW);
        $this->_oxidOrder->save();
    }

    /**
     * @return string
     */
    public function getFormattedAvailableRefundAmount()
    {
        $price =  $this->_oxidOrder->oxorder__payo_refund_available->value
            ? $this->_oxidOrder->oxorder__payo_refund_available->value
            : '0.00';
        $aCurrencies = Registry::getConfig()->getCurrencyArray();
        $cur = null;

        foreach ($aCurrencies as $currency) {
            if ($currency->name == $this->_oxidOrder->oxorder__oxcurrency) {
                $cur = $currency;
            }
        }

        return Registry::getLang()->formatCurrency($price, $cur);
    }
    
    /**
     * Get available refund amount
     *
     * @return string
     */
    public function availableRefundAmount()
    {
        return $this->_oxidOrder->oxorder__payo_refund_available->value
            ? $this->_oxidOrder->oxorder__payo_refund_available->value
            : '0.00';
    }

    /**
     * Method returns available
     *
     * @return string
     */
    public function availableCapturePrice()
    {
        return bcsub($this->preauthPrice(), $this->totalCapturedPrice(), 2);
    }

    /**
     * Whether or not order has not shipped items left
     *
     * @param array $shippedItems
     *
     * @return bool
     */
    public function hasUnshippedItems($shippedItems)
    {
        $basketItems = $this->orderingContext()->basket()->items();
        if (count($basketItems)) {
            if (empty($shippedItems)) {
                return true;
            }

            foreach ($basketItems as $basketItem) {
                $amount = $basketItem->amount;
                foreach ($shippedItems as $key => $shippedAmount) {
                    if ($key == $basketItem->articleId) {
                        $amount = bcsub($amount, $shippedAmount, 5);
                        break;
                    }
                }

                if ($amount != 0) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Update database order shipments table on partial delivery button click
     *
     * @param array $items
     */
    public function updateDbOnPartialShip(array $items)
    {
        $db = Db::getDb(Db::FETCH_MODE_ASSOC);

        $sql = 'INSERT INTO `payo_ordershipments` (`oxid`, `item_id`, `amount`) 
                VALUES (?, ?, ?) ON DUPLICATE KEY 
                UPDATE `oxid` = ?, `item_id` = ?, `amount` = LEAST(?, `amount` + ?)';

        foreach ($items as $key => $amount) {
            $orderArticle = $this->findOrderArticle($key);

            $db->execute($sql, [
                $this->getId(),
                $key,
                $amount,
                $this->getId(),
                $key,
                $orderArticle->amount,
                $amount,
            ]);
        }
    }

    /**
     * Get basket item by ID
     *
     * @param string $key
     *
     * @return bool|BasketItem
     */
    private function findOrderArticle($key)
    {
        $orderArticles = isset($this->orderArticles)
            ? $this->orderArticles
            : $this->orderingContext()->basket()->items();

        if (count($orderArticles)) {
            foreach ($orderArticles as $orderArticle) {
                if ($orderArticle->articleId == $key) {
                    return $orderArticle;
                }
            }
        }

        return false;
    }

    /**
     * Update database order shipments table on full delivery button click
     */
    public function updateDbOnFullShip()
    {
        $db = Db::getDb(Db::FETCH_MODE_ASSOC);

        $sql = 'INSERT INTO `payo_ordershipments` (`oxid`, `item_id`, `amount`) 
                VALUES (?, ?, ?) ON DUPLICATE KEY 
                UPDATE `oxid` = ?, `item_id` = ?, `amount` = LEAST(?, `amount` + ?)';

        $orderArticles = $this->orderingContext()->basket()->items();
        if (count($orderArticles)) {
            $shippedItems = $this->loadShippedItems();

            /** @var BasketItem $article */
            foreach ($orderArticles as $article) {
                $key = $article->articleId;
                $params = [
                  $this->getId(),
                  $key,
                  $article->amount,
                  $this->getId(),
                  $key,
                  $article->amount,
                  isset($shippedItems[$key])
                      ? bcsub($article->amount, $shippedItems[$key])
                      : $article->amount
                ];

                $db->execute($sql, $params);
            }
        }
    }

    /**
     * Get shipped items and their amounts
     *
     * @return array
     */
    private function loadShippedItems()
    {
        $db = Db::getDb(Db::FETCH_MODE_ASSOC);
        $sql = 'SELECT * FROM `payo_ordershipments` WHERE `oxid` = ?';
        $_shippedArticles = $db->getAll($sql, [$this->getId()]);
        $shippedArticles = [];

        if (count($_shippedArticles)) {
            foreach ($_shippedArticles as $item) {
                $shippedArticles[$item['item_id']] = $item['amount'];
            }
        }

        return $shippedArticles;
    }
}
