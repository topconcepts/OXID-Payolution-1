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
namespace TopConcepts\Payolution\Module\Controller\Admin\Order;

use OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController;
use OxidEsales\Eshop\Application\Controller\Admin\OrderOverview;
use OxidEsales\Eshop\Application\Model\Order;
use OxidEsales\Eshop\Core\DatabaseProvider as Db;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\Request;
use OxidEsales\Eshop\Core\UtilsView;
use TopConcepts\Payolution\AccessPoint;
use TopConcepts\Payolution\AdminEvents;
use TopConcepts\Payolution\Basket\BasketItem;
use TopConcepts\Payolution\Exception\PayolutionException;
use TopConcepts\Payolution\Order\PayolutionOrder;
use TopConcepts\Payolution\PayolutionModule;
use TopConcepts\Payolution\Utils\FormatterUtils;

/**
 * Class Payolution_Orders. New tab for Order management page in OXID backend
 *
 * Class OrdersController
 * @package TopConcepts\Payolution\Module\Controllers\Admin\Order
 */
class OrdersController extends AdminDetailsController
{
    /**
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = 'payolution_orders.tpl';

    /**
     * Edit object
     *
     * @var Order
     */
    private $_oEditObject = null;

    /**
     * @var PayolutionModule
     */
    private $payolutionModule;

    /**
     * @var AdminEvents
     */
    private $adminEvents;

    /**
     * OrdersController constructor.
     */
    public function __construct()
    {
        /* @var $ap AccessPoint */
        $ap = oxNew(AccessPoint::class);
        $this->payolutionModule = $ap->getModule();
        $this->adminEvents = $this->payolutionModule->adminEvents();
    }

    /**
     * Executes parent mathod parent::render(), creates oxorder, passes
     * it's data to Smarty engine and returns name of template file
     * "payolution_orders.tpl".
     *
     * @return string
     */
    public function render()
    {
        $db = Db::getDb(Db::FETCH_MODE_ASSOC);
        $schema = Registry::getConfig()->getConfigParam('dbName');
        $sql = 'SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = "'.$schema.'" AND  TABLE_NAME = "payo_history"';

        $exists = $db->getOne($sql);

        if (!$exists) {
            Registry::get(UtilsView::class)->addErrorToDisplay(
                Registry::getLang()->translateString('PAYOLUTION_INSTALL_NOT_INSTALLED')
            );

            return 'payolution_empty.tpl';
        }

        $this->_getEditObject();
        $this->_getPartialOrderInfo();
        $this->_getOrderHistory();

        parent::render();

        return $this->_sThisTemplate;
    }

    /**
     * Get edit object
     *
     * @return Order
     */
    protected function _getEditObject()
    {
        if ($this->_oEditObject === null) {
            $oOrder = oxNew(Order::class);
            $sOxid  = $this->getEditObjectId();
            if ($sOxid != "-1" && isset($sOxid)) {
                // load object
                $oOrder->load($sOxid);
                $this->_oEditObject       = $oOrder;
                $this->_aViewData["edit"] = $oOrder;
            }
        }

        return $this->_oEditObject;
    }

    /**
     * Load shipped items
     *
     * @return void
     */
    protected function _getPartialOrderInfo()
    {
        if (!$this->getOrder()) {
            return;
        }

        $this->_aViewData['partialItems'] = array();
        $db = Db::getDb(Db::FETCH_MODE_ASSOC);
        $items = $db->getAll(
            'SELECT `item_id`, `amount` FROM `payo_ordershipments` WHERE `oxid` = ?',
            array($this->getOrder()->getId())
        );
        if (count($items)) {
            foreach ($items as $item) {
                $this->_aViewData['partialItems'][$item['item_id']] = $item['amount'];
            }
        }
    }

    /**
     * Load order history
     */
    protected function _getOrderHistory()
    {
        $db = Db::getDb(Db::FETCH_MODE_ASSOC);
        $sql = 'SELECT * FROM `payo_history` WHERE `order_id` = ? ORDER BY `added_at` DESC';
        $history = $db->getAll($sql, [$this->getEditObjectId()]);

        /** @var FormatterUtils $formatter */
        $formatter = $this->payolutionModule->formatter();

        if (count($history)) {
            foreach ($history as $record) {
                $params = json_decode($record['HISTORY_VALUES'], true);
                $aCurrencies = $this->getConfig()->getCurrencyArray();
                $cur = null;

                foreach ($aCurrencies as $currency) {
                    if ($currency->name == $params['currency']) {
                        $cur = $currency;
                    }
                }

                if (isset($params['price'])) {
                    $params['price'] = Registry::getLang()->formatCurrency($params['price'], $cur);
                }

                $this->_aViewData['history'][] = array(
                    'status' => $this->translateOrderHistory($record['STATUS'], $params),
                    'date'   => $formatter->date($record['ADDED_AT']),
                    'time'   => $formatter->time($record['ADDED_AT'])
                );
            }
        }
    }

    /**
     * @param string $status
     * @param array $params
     *
     * @return string
     */
    private function translateOrderHistory($status, $params)
    {
        $msg = Registry::getLang()->translateString('PAYOLUTION_ORDERS_STATUS_'.$status);

        return vsprintf($msg, $params ? $params : array());
    }

    /**
     * @return null|PayolutionOrder
     */
    private function getOrder()
    {
        return $this->payolutionModule->asPayolutionOrder($this->_getEditObject());
    }

    /**
     * @return void
     */
    public function cancelOrder()
    {
        try {
            $this->payolutionModule->adminEvents()->onCancel($this->getOrder());
            $this->successMessage('PAYOLUTION_SUCCESS_ORDER_CANCELED');
        } catch (PayolutionException $e) {
            $this->errorMessage($e);
        }
    }

    /**
     * @return void
     */
    public function refundOrder()
    {
        try {
            $request = Registry::get(Request::class);
            $refundAmount = $request->getRequestParameter('refundAmount');
            if($refundAmount > 0) {
                $this->payolutionModule->adminEvents()->onRefund($this->getOrder(), $refundAmount);
                $this->successMessage('PAYOLUTION_SUCCESS_ORDER_REFUND');
            }
        } catch (PayolutionException $e) {
            $this->errorMessage($e);
        }
    }

    /**
     * @return void
     */
    public function pdf()
    {
        $_POST['pdflanguage'] = $this->_iEditLang;
        $_POST['pdftype'] = 'standart';

        /* @var $orderOverview OverviewController */
        $orderOverview = oxNew(OrderOverview::class);
        $orderOverview->createPDF();

        /**
         * cl	order_overview
            fnc	createPDF
            force_admin_sid	5foi4mo0hqvfj2jmnjm4pgri91
            oxid	31e7ed7ed6c65d9907c9de5189708c1a
            pdflanguage	0
            pdftype	standart
            save	Create PDF
            stoken	136960C8
         */
    }

    /**
     * @return void
     */
    public function shipOrder()
    {
        try {
            $this->payolutionModule->adminEvents()->onShipped($this->getOrder());
            $this->successMessage('PAYOLUTION_SUCCESS_ORDER_SHIPPED');
        } catch (PayolutionException $e) {
            $this->errorMessage($e);
        }
    }

    /**
     * @return void
     */
    public function shipOrderPartially()
    {
        try {
            $request = Registry::get(Request::class);
            $items = $request->getRequestParameter('orderArticle');
            $amounts = $request->getRequestParameter('articleAmount');
            $price = 0;
            //not used: example only: $currency = $this->getOrder()->oxidOrder()->getOrderCurrency()->sign;
            bcscale(2);

            $orderArticles = $this->getOrder()->orderingContext()->basket()->items();
            if (count($orderArticles)) {
                /** @var BasketItem $article */
                foreach ($orderArticles as $article) {
                    $key = $article->articleId;
                    if (isset($items[$key])) {
                        $_price      = $article->pricePerItem;
                        $items[$key] = $amounts[$key];
                        $_price      = bcmul($_price, $items[$key]);
                        $price       = bcadd($price, $_price);
                    }
                }
            }

            $this->payolutionModule->adminEvents()->onPartiallyShipped($this->getOrder(), $price, $items);

            $this->successMessage('PAYOLUTION_SUCCESS_ORDER_SHIPPED_PARTIALLY');
        } catch (PayolutionException $e) {
            $this->errorMessage($e);
        }
    }

    /**
     * @param string $msg
     *
     * @return void
     */
    private function successMessage($msg)
    {
        $this->_aViewData['successMessage'] = Registry::getLang()->translateString($msg);
    }

    private function errorMessage(PayolutionException $e)
    {
        $errorText = '';

        if ($e->responseError()) {
            $responseError = $e->responseError();
            $errorText = $responseError->status . ' :: '. $responseError->reason . ' :: ' .$responseError->message . ' ('. $responseError->messageCode. ')';
        }

        $e->setMessage($e->getMessage() . ' '.$errorText);
        Registry::get(UtilsView::class)->addErrorToDisplay($e, false, true);
    }


    /**
     * Unknown unknowns.... but oxid crashes without these
     *
     * @return bool
     */
    public function getIsOrderStep()
    {
        return false;
    }

    /**
     * Unknown unknowns.... but oxid crashes without these
     *
     * @return string
     */
    public function getLink()
    {
        return '';
    }

    /**
     * @return bool
     */
    public function isOrderPdfEnabled()
    {
        if (class_exists('MyOrder') || class_exists('InvoicepdfOxOrder')) {
            return true;
        }

        return false;
    }
}
