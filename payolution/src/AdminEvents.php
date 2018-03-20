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
namespace TopConcepts\Payolution;

use TopConcepts\Payolution\Exception\PayolutionException;
use TopConcepts\Payolution\Manager\OrderManager;
use TopConcepts\Payolution\Order\OrderStatus;
use TopConcepts\Payolution\Order\PayolutionOrder;

/**
 * Class Payolution_AdminEvents describes all events which occurs in backend
 * (by system administrator)
 *
 * The buttons which triggers these events should be visible in Admin -> Orders
 * -> Order item -> tab 'Payolution'
 *
 * Class AdminEvents
 * @package Payolution
 */
class AdminEvents
{
    /**
     * @var PayolutionServices
     */
    private $services;

    /**
     * @var OrderManager
     */
    private $orderingManager;

    /**
     * @param PayolutionServices $services
     */
    public function __construct(PayolutionServices $services)
    {
        $this->services        = $services;
        $this->orderingManager = $services->orderingManager();
    }

    /**
     * Event is called when cancel delivery button is clicked
     *
     * @param PayolutionOrder $order
     *
     * @throws PayolutionException
     */
    public function onCancel(PayolutionOrder $order)
    {
        $this->orderingManager->cancel($order);
    }

    /**
     * Event is called when refund button is clicked
     *
     * @param PayolutionOrder $order
     * @param float $refundAmount
     */
    public function onRefund(PayolutionOrder $order, $refundAmount)
    {
        $this->orderingManager->refund($order, $refundAmount);
    }

    /**
     * Event is called when shipped button is clicked
     *
     * @param PayolutionOrder $order
     *
     * @throws PayolutionException
     */
    public function onShipped(PayolutionOrder $order)
    {
        $this->orderingManager->shipped($order);
    }

    /**
     * Event is called when partial ship button is clicked
     *
     * @param PayolutionOrder $order
     * @param float $price
     * @param array $items
     *
     * @throws PayolutionException
     */
    public function onPartiallyShipped(PayolutionOrder $order, $price, $items)
    {
        $this->orderingManager->partlyShipped($order, $price);
        $order->updateDbOnPartialShip($items);
    }

    /**
     * Event is called when cancel or delete buttons are clicked on order list
     *
     * @param PayolutionOrder $order
     * @throws PayolutionException
     */
    public function onCancelOrDelete(PayolutionOrder $order)
    {
        if ($order->status() != OrderStatus::Refunded() &&
            $order->status() != OrderStatus::Cancelled() &&
            $order->status() != OrderStatus::Prechecked() &&
            $order->status() != OrderStatus::Unknown()
        )
        {
            $this->orderingManager->cancelOrRefund($order);
        }
    }
}
