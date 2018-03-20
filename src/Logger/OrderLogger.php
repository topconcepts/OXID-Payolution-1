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
namespace TopConcepts\Payolution\Logger;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\UtilsDate;
use TopConcepts\Payolution\Module\Model\HistoryModel;
use TopConcepts\Payolution\Order\OrderStatus;
use TopConcepts\Payolution\Order\PayolutionOrder;

/**
 * Class OrderLogger
 * @package TopConcepts\Payolution\Logger
 */
class OrderLogger
{
    /**
     * @var PayolutionOrder
     */
    private $orderInProgress;

    /**
     * @param OrderStatus $status
     * @param PayolutionOrder $order
     * @param array $values
     */
    public function logStatusChange(OrderStatus $status, PayolutionOrder $order, $values = [])
    {
        HistoryModel::create()
          ->setOrderId($order->getId())
          ->setStatus($status->name())
          ->setHistoryValues($values)
          ->setAddedAt(date('Y-m-d H:i:s', Registry::get(UtilsDate::class)->getTime()))
          ->save();
    }


    /**
     * @return PayolutionOrder|null
     */
    public function _getOrderInProgress()
    {
        return $this->orderInProgress;
    }

    /**
     * @param PayolutionOrder $order
     */
    public function setOrderInProgress(PayolutionOrder $order)
    {
        $this->orderInProgress = $order;
    }
} 
