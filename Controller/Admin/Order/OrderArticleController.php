<?php
/**
 * Copyright 2017 Payolution GmbH
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
namespace Payolution\Module\Controller\Admin\Order;

use OxidEsales\Eshop\Application\Controller\Admin\OrderArticle;
use OxidEsales\Eshop\Application\Model\Order;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\UtilsView;
use Payolution\AccessPoint;
use Payolution\Exception\PayolutionException;

/**
 * Class OrderArticleController
 * @see OrderArticle
 * @mixin OrderArticle
 * @package Payolution\Module\Controllers\Admin\Order
 */
class OrderArticleController extends OrderArticleController_Parent
{
    /**
     * @see OrderArticle::addThisArticle()
     * @return void
     */
    public function addThisArticle()
    {
        parent::addThisArticle();
        $this->updatePayolutionOrder();
    }

    /**
     * @see OrderArticle::updateOrder()
     * @return void
     */
    public function updateOrder() {
        parent::updateOrder();
        $this->updatePayolutionOrder();
    }

    /**
     * @see OrderArticle::deleteThisArticle()
     * @return void
     */
    public function deleteThisArticle()
    {
        parent::deleteThisArticle();
        $this->updatePayolutionOrder();
    }

    /**
     * @return void
     */
    private function updatePayolutionOrder() {
        $orderId = $this->getEditObjectId();
        /** @var Order $order */
        $order = oxNew(Order::class);
        $order->load($orderId);

        if ($order->isLoaded() && $order->isPayolutionOrder()) {
            $this->sendOrderDataToPayolution($order);
        }
    }

    /**
     * @param Order $order
     */
    private function sendOrderDataToPayolution($order)
    {
        /* @var $module AccessPoint */
        $module = oxNew(AccessPoint::class)->getModule();
        try {
            $order = $module->asPayolutionOrder($order);
            $module->events()->onOrderUpdate($order);
        } catch (PayolutionException $e) {
            Registry::get(UtilsView::class)->addErrorToDisplay($e, false, true);
        }
    }
}
