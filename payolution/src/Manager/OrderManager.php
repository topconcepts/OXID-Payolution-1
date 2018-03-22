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

use OxidEsales\Eshop\Application\Model\Basket;
use OxidEsales\Eshop\Application\Model\Order;
use OxidEsales\Eshop\Application\Model\Payment;
use OxidEsales\Eshop\Application\Model\User;
use OxidEsales\Eshop\Application\Model\UserPayment;
use OxidEsales\Eshop\Core\Registry;
use TopConcepts\Payolution\Client\ApiInterface;
use TopConcepts\Payolution\Client\Response\Response;
use TopConcepts\Payolution\Client\Type\PriceType;
use TopConcepts\Payolution\Logger\OrderLogger;
use TopConcepts\Payolution\Exception\PayolutionException;
use TopConcepts\Payolution\Order\OrderContext;
use TopConcepts\Payolution\Order\OrderStatus;
use TopConcepts\Payolution\Order\PayolutionOrder;
use TopConcepts\Payolution\Payment\PaymentMethod;
use TopConcepts\Payolution\Utils\TransformUtils;
use TopConcepts\Payolution\Validation\ServiceValidation;

/**
 * Class OrderManager
 * @package TopConcepts\Payolution\Manager
 */
class OrderManager
{
    /**
     * @var ApiInterface
     */
    private $api;

    /**
     * @var ConfigManager
     */
    private $config;

    /**
     * @var FormManager
     */
    private $formManager;

    /**
     * @var ServiceValidation
     */
    private $validationService;

    /**
     * @var TransformUtils
     */
    private $transformUtils;

    /**
     * @var OrderLogger
     */
    private $logger;

    /**
     * @param ApiInterface $api
     * @param ConfigManager $configManager
     * @param FormManager $formManager
     * @param ServiceValidation $validationService
     * @param OrderLogger $logger
     */
    public function __construct(
        ApiInterface $api,
        ConfigManager $configManager,
        FormManager $formManager,
        ServiceValidation
        $validationService,
        OrderLogger $logger
    )
    {
        $this->api               = $api;
        $this->config            = $configManager->getConfig();
        $this->formManager       = $formManager;
        $this->validationService = $validationService;
        $this->transformUtils    = oxNew(TransformUtils::class);
        $this->logger            = $logger;
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
    public function createOrderingContext(
        $user,
        $userIpAddress,
        $basket = null,
        $payment = null,
        $paymentOptions = null
    )
    {
        return oxNew(
            OrderContext::class,
            $this->formManager,
            $user,
            $userIpAddress,
            $basket,
            $payment,
            $paymentOptions
        );
    }


    /**
     * Method check if specified order is Payolution order (payolution payment
     * method is selected)
     *
     * @param Order $order
     *
     * @return bool
     */
    public function isPayolutionOrder($order)
    {
        /* @var $userPayment UserPayment */
        $userPayment = $order->getPaymentType();
        $paymentId = $userPayment ? $userPayment->oxuserpayments__oxpaymentsid->value : '';

        return preg_match('/^payolution_/', $paymentId);
    }

    /**
     * @param Payment $oxidPayment
     *
     * @return PaymentMethod|null
     */
    public function asPaymentMethod(Payment $oxidPayment)
    {
        return PaymentMethod::fromString($oxidPayment->getId());
    }

    /**
     * Method returns payolution object instance if order is payolution order
     * or null if not.
     *
     * @param Order $order
     *
     * @return PayolutionOrder|null
     */
    public function asPayolutionOrder($order)
    {
        return $this->isPayolutionOrder($order)
            ? PayolutionOrder::from($order, $this, $this->formManager)
            : null;
    }

    /**
     * Payolution: PC - precheck of creditworthyness of customer before purchase
     *
     * @param PayolutionOrder $order
     *
     * @throws PayolutionException
     */
    public function precheck(PayolutionOrder $order)
    {
        $this->assertAllowedTransitionTo($order, OrderStatus::Prechecked());
        $this->logger->setOrderInProgress($order);
        $responseId = $this->precheckByContext($order->orderingContext());

        $order->setPrecheckId($responseId);
        $context = $order->orderingContext();
        $payment = $this->transformUtils->getPaymentTypeFromContext($context);

        $order->setStatus(OrderStatus::Prechecked());
        $this->logger->logStatusChange(OrderStatus::Prechecked(), $order, [
                'price'    => $payment->amount,
                'currency' => $payment->currency,
            ]);
    }

    /**
     * Payolution: PC - precheck of creditworthyness of customer before purchase
     *
     * @param OrderContext $context
     *
     * @throws PayolutionException
     * @return string precheck response Id
     */
    public function precheckByContext(OrderContext $context)
    {
        $this->validationService->validate($context);

        $customer    = $this->transformUtils->getCustomerTypeFromContext($context);
        $payment     = $this->transformUtils->getPaymentTypeFromContext($context);
        $basketItems = $this->transformUtils->getBasketItemsFromContext($context);

        $payment->calculationId = $this->getCalculationId($context);

        $response = $this->throwIfError(
            $this->api->precheck($context->paymentMethod(), $customer, $payment, $basketItems)
        );

        return $response->responseId();
    }

    /**
     * @param OrderContext $context
     *
     * @return string|null
     * @throws PayolutionException
     */
    private function getCalculationId(OrderContext $context)
    {
        $calculationId = null;

        if ($context->paymentMethod() == PaymentMethod::Installment()) {
            /** @var PriceType $price */
            $price = oxNew(PriceType::class);
            $price->setPrice($context->basket()->totalOrderPrice());

            $clResponse = $this->api->calculate($price, $context->userCountry());

            $calculationId = $clResponse->uniqueId();

            if (!$calculationId) {
                $this->throwError(PayolutionException::PAYOLUTION_REMOTE_ERROR_RESPONSE);
            }
        }

        return $calculationId;
    }

    /**
     * Payolution: PA - creation of order
     *
     * @param PayolutionOrder $order
     *
     * @throws PayolutionException
     */
    public function confirm(PayolutionOrder $order)
    {
        $this->assertAllowedTransitionTo($order, OrderStatus::Created());
        $this->logger->setOrderInProgress($order);

        $context = $order->orderingContext();

        $customer    = $this->transformUtils->getCustomerTypeFromContext($context);
        $payment     = $this->transformUtils->getPaymentTypeFromContext($context);
        $basketItems = $this->transformUtils->getBasketItemsFromContext($context);

        $precheckId = $order->precheckId();

        $payment->invoiceId     = $order->invoiceNo();
        $payment->calculationId = $this->getCalculationId($context);

        $response = $this->throwIfError(
            $this->api->preauth($precheckId, $context->paymentMethod(), $customer,
                $payment, $basketItems)
        );

        $order->setPreauthId($response->responseId());
        $order->setPaymentReferenceId($response->paymentReferenceId());
        $order->setPreauthPrice($payment->amount);
        $order->setStatus(OrderStatus::Created());

        $this->logger->logStatusChange(OrderStatus::Created(), $order, [
                'price'    => $payment->amount,
                'currency' => $payment->currency,
            ]);
    }

    /**
     * Payolution PZ - Update of order number or change amount before shipment
     *
     * @param PayolutionOrder $order
     * @param bool $isContentUpdate
     */
    public function update(PayolutionOrder $order, $isContentUpdate = false)
    {
        if ($order->status() != OrderStatus::Created()) {
            $this->throwError(PayolutionException::ORDER_STATUS_TRANSITION_NOT_ALLOWED);
        }
        $this->logger->setOrderInProgress($order);

        $context = $order->orderingContext();

        $payment     = $this->transformUtils->getPaymentTypeFromContext($context);
        $basketItems = $this->transformUtils->getBasketItemsFromContext($context);

        $preauthId = $order->preauthId();

        $payment->invoiceId = $order->invoiceNo();

        $this->throwIfError(
            $this->api->update($preauthId, $context->paymentMethod(), $payment, $basketItems)
        );

        $order->setPreauthPrice($payment->amount);

        if ($isContentUpdate) {
            $this->logger->logStatusChange(OrderStatus::Updated(), $order, [
                    'price' => $payment->amount,
                    'currency' => $payment->currency,]);
        }   
        
        $this->api->invalidateAllCache();
    }

    /**
     * Payolution: CP - shipment
     *
     * @param PayolutionOrder $order
     *
     * @throws PayolutionException
     */
    public function shipped(PayolutionOrder $order)
    {
        $this->assertAllowedTransitionTo($order, OrderStatus::Shipped());
        $this->logger->setOrderInProgress($order);

        $context = $order->orderingContext();

        $payment = $this->transformUtils->getPaymentTypeFromContext($context);

        $amount = $order->availableCapturePrice();

        $this->__partlyShipped($order, $amount, $payment->currency); // <- identical as partly shipped.

        $order->updateDbOnFullShip();

        $order->setStatus(OrderStatus::Shipped());
        $this->logger->logStatusChange(OrderStatus::Shipped(), $order, [
                'price'    => $amount,
                'currency' => $payment->currency,
            ]);
    }

    /**
     * Payolution: CP - partly shipment
     *
     * @param PayolutionOrder $order
     * @param float $price
     *
     * @throws PayolutionException
     */
    public function partlyShipped(PayolutionOrder $order, $price)
    {
        $this->assertAllowedTransitionTo($order, OrderStatus::PartlyShipped());
        $this->logger->setOrderInProgress($order);

        $context = $order->orderingContext();

        $payment = $this->transformUtils->getPaymentTypeFromContext($context);

        $currency = $payment->currency;

        $this->__partlyShipped($order, $price, $currency);

        $order->setStatus(OrderStatus::PartlyShipped());

        $this->logger->logStatusChange(OrderStatus::PartlyShipped(), $order, [
            'price' => $price,
            'currency' => $currency,
            ]);
    }

    /**
     * @param PayolutionOrder $order
     *
     * @throws PayolutionException
     */
    public function cancelOrRefund(PayolutionOrder $order)
    {
        if ($order->status() == OrderStatus::Created()) {
            $this->cancel($order);
        } else {
            $this->fullRefund($order);
        }
    }

    /**
     * Payolution: RV - cancel before shipment
     *
     * @param PayolutionOrder $order
     * @throws PayolutionException
     */
    public function cancel(PayolutionOrder $order)
    {
        $this->assertAllowedTransitionTo($order, OrderStatus::Cancelled());
        $this->logger->setOrderInProgress($order);

        $context   = $order->orderingContext();
        $preauthId = $order->preauthId();

        $payment = $this->transformUtils->getPaymentTypeFromContext($context);

        $payment->invoiceId = $order->invoiceNo();

        $this->throwIfError($this->api->reverse($preauthId, $payment));

        $order->setStatus(OrderStatus::Cancelled());
        $this->logger->logStatusChange(OrderStatus::Cancelled(), $order, [
                'price'    => $payment->amount,
                'currency' => $payment->currency,
            ]);
    }

    /**
     * Payolution: RF - refund
     *
     * @param PayolutionOrder $order
     * @param float $refundAmount
     */
    public function refund(PayolutionOrder $order, $refundAmount)
    {
        if (abs($refundAmount - $order->availableRefundAmount()) < 0.001) {
            $this->fullRefund($order);
        } else {
            $this->partlyRefund($order, $refundAmount);
        }
    }

    /**
     * Payolution: RF - refund
     *
     * @param PayolutionOrder $order
     *
     * @throws PayolutionException
     */
    private function fullRefund(PayolutionOrder $order)
    {
        $this->assertAllowedTransitionTo($order, OrderStatus::Refunded());
        $this->logger->setOrderInProgress($order);

        $context = $order->orderingContext();
        $payment = $this->transformUtils->getPaymentTypeFromContext($context);

        $refundAmount = $order->availableRefundAmount();

        $this->__partlyRefund($order, $refundAmount, $payment->currency);

        $order->setStatus(OrderStatus::Refunded());
        $this->logger->logStatusChange(OrderStatus::Refunded(), $order, [
                'price'    => $refundAmount,
                'currency' => $payment->currency,
            ]);
    }


    /**
     * Payolution: RF - partly refund
     *
     * @param PayolutionOrder $order
     * @param $price
     * @param $currency
     * @throws PayolutionException
     */
    private function partlyRefund(PayolutionOrder $order, $price, $currency = null)
    {
        $this->assertAllowedTransitionTo($order,
            OrderStatus::PartlyRefunded());
        $this->logger->setOrderInProgress($order);

        if (!$currency) {
            $context  = $order->orderingContext();
            $payment  = $this->transformUtils->getPaymentTypeFromContext($context);
            $currency = $payment->currency;
        }

        $this->__partlyRefund($order, $price, $currency);

        $order->setStatus(OrderStatus::PartlyRefunded());

        $this->logger->logStatusChange(OrderStatus::PartlyRefunded(), $order, [
            'price' => $price,
            'currency' => $currency,
        ]);
    }

    /**
     * Payolution: CP - partly shipment
     *
     * @param PayolutionOrder $order
     * @param  float                              $price
     * @param  string                             $currency
     */
    private function __partlyShipped(PayolutionOrder $order, $price, $currency)
    {
        $context   = $order->orderingContext();
        $preauthId = $order->preauthId();

        $payment = $this->transformUtils->getPaymentTypeFromContext($context);

        $payment->amount   = $price;
        $payment->currency = $currency;

        $payment->invoiceId = $order->invoiceNo();

        $response = $this->throwIfError($this->api->capture($preauthId, $payment));

        $order->setCaptureId($response->responseId());

        $availableRefundAmount = bcadd($order->availableRefundAmount(), $price, 2);
        $order->setAvailableRefundAmount($availableRefundAmount);

        $totalCapturedPrice = bcadd($order->totalCapturedPrice(), $price, 2);
        $order->setTotalCapturedPrice($totalCapturedPrice);
    }

    /**
     * Payolution: RF - partly refund
     *
     * @param PayolutionOrder $order
     * @param $price
     * @param $currency
     */
    private function __partlyRefund(PayolutionOrder $order, $price, $currency)
    {
        $context   = $order->orderingContext();
        $captureId = $order->captureId();

        $payment = $this->transformUtils->getPaymentTypeFromContext($context);

        $payment->amount   = $price;
        $payment->currency = $currency;

        $payment->invoiceId = $order->invoiceNo();

        $response = $this->throwIfError($this->api->refund($captureId, $payment));

        $order->setCaptureId($response->responseId());

        $availableRefundAmount = bcsub($order->availableRefundAmount(), $price, 2);
        $order->setAvailableRefundAmount($availableRefundAmount);
    }

    /**
     * @param Response $response
     *
     * @return Response
     */
    private function throwIfError(Response $response)
    {
        if (!$response->success()) {
            $this->throwResponseError($response);
        }

        return $response;
    }

    /**
     * Throws PayolutionException exception which is determined by erroneous
     * response from Payolution API
     *
     * @param Response $response
     */
    private function throwResponseError(Response $response)
    {
        if($error = $response->error()){
            Registry::getSession()->setVariable('payolutionPaymentError', (string)$error->messageCode);
        }

        PayolutionException::throwResponseError($response->error());
    }

    /**
     * Throws PayolutionException exception with specified code.
     * Codes are available as constants in PayolutionException class.
     *
     * @param int $code
     *
     */
    private function throwError($code)
    {
        PayolutionException::throwError($code);
    }

    /**
     * @param PayolutionOrder $order
     * @param OrderStatus     $status
     *
     */
    private function assertAllowedTransitionTo(PayolutionOrder $order, OrderStatus $status)
    {

        $transitionMap = [
            // allowed: from status --> to status
            OrderStatus::Unknown()->name() => [
                OrderStatus::Prechecked()
            ],
            OrderStatus::Prechecked()->name() => [
                OrderStatus::Created()
            ],
            OrderStatus::Created()->name() => [
                OrderStatus::Cancelled(),
                OrderStatus::Shipped(),
                OrderStatus::PartlyShipped(),
            ],
            OrderStatus::Shipped()->name() => [
                OrderStatus::Refunded(),
                OrderStatus::PartlyRefunded(),
            ],
            OrderStatus::PartlyShipped()->name() => [
                OrderStatus::Refunded(),
                OrderStatus::PartlyRefunded(),
                OrderStatus::PartlyShipped(),
                OrderStatus::Shipped(),
            ],
            OrderStatus::Cancelled()
                ->name() => [// empty. no possible statuses
            ],
            OrderStatus::Refunded()
                ->name() => [// empty. no possible statuses
                OrderStatus::Shipped(),
                OrderStatus::PartlyShipped(),
            ],
            OrderStatus::PartlyRefunded()
                ->name() => [// empty. no possible statuses
                OrderStatus::Refunded(),
                OrderStatus::PartlyRefunded(),
                OrderStatus::Shipped(),
                OrderStatus::PartlyShipped(),
            ],
        ];


        if ($order->paymentMethod() === PaymentMethod::Installment()) {
            $transitionMap[OrderStatus::Created()->name()][] = OrderStatus::Refunded();
            $transitionMap[OrderStatus::Created()->name()][] = OrderStatus::PartlyRefunded();
        }

        $current = $order->status();

        $allowed = (isset($transitionMap[$current->name()]) && in_array($status, $transitionMap[$current->name()]));

        if (!$allowed) {
            $this->throwError(PayolutionException::ORDER_STATUS_TRANSITION_NOT_ALLOWED);
        }
    }
}
