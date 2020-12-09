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


/**
 * Metadata version
 */

use OxidEsales\Eshop\Application\Controller\Admin\OrderAddress;
use OxidEsales\Eshop\Application\Controller\Admin\OrderArticle;
use OxidEsales\Eshop\Application\Controller\Admin\OrderMain;
use OxidEsales\Eshop\Application\Controller\Admin\OrderOverview;
use OxidEsales\Eshop\Application\Controller\OrderController;
use OxidEsales\Eshop\Application\Model\CountryList;
use OxidEsales\Eshop\Application\Model\PaymentGateway;
use OxidEsales\Eshop\Application\Model\PaymentList;
use OxidEsales\Eshop\Core\Email;
use OxidEsales\Eshop\Core\ShopControl;
use OxidEsales\Eshop\Core\ViewConfig;
use TopConcepts\Payolution\Module\Controller\Admin\ApiLog\DetailsController;
use TopConcepts\Payolution\Module\Controller\Admin\ApiLog\ListController;
use TopConcepts\Payolution\Module\Controller\Admin\ApiLog\MainController;
use TopConcepts\Payolution\Module\Controller\Admin\ConfigController;
use TopConcepts\Payolution\Module\Controller\Admin\ExpertController;
use TopConcepts\Payolution\Module\Controller\Admin\InstallController;
use TopConcepts\Payolution\Module\Controller\Admin\JsLibraryController;
use TopConcepts\Payolution\Module\Controller\Admin\Order\OrderAddressController;
use TopConcepts\Payolution\Module\Controller\Admin\Order\OrderArticleController;
use TopConcepts\Payolution\Module\Controller\Admin\Order\OrdersController;
use TopConcepts\Payolution\Module\Controller\Admin\Order\OverviewController;
use TopConcepts\Payolution\Module\Controller\Admin\RegionalController;
use TopConcepts\Payolution\Module\Controller\PdfDownloadController;
use TopConcepts\Payolution\Module\Controller\ViewConfigController;
use TopConcepts\Payolution\Module\Model\CountryListModel;
use TopConcepts\Payolution\Module\Model\CountryModel;
use TopConcepts\Payolution\Module\Model\OrderArticleModel;
use TopConcepts\Payolution\Module\Model\OrderModel;
use TopConcepts\Payolution\Module\Model\PaymentGatewayModel;
use TopConcepts\Payolution\Module\Model\PaymentListModel;
use TopConcepts\Payolution\Module\Model\PaymentModel;

$sMetadataVersion = '2.1';

/**
 * Module information
 */
$aModule = [
    'id' => 'payolution',
    'title' => 'Payolution',
    'description' => 'Payolution payment module',
    'version' => '1.5.0',
    'author' => 'Payolution',
    'thumbnail' => 'logo.png',
    'url' => 'http://www.payolution.com',
    'email' => 'info@payolution.com',
    'controllers' => [
        // frontend controllers
        'PayolutionPdfDownload' => PdfDownloadController::class,
        // admin controllers
        'payolution_regional' => RegionalController::class,
        'payolution_expert' => ExpertController::class,
        'payolution_config' => ConfigController::class,
        'payolution_orders' => OrdersController::class,
        'payolution_apilog' => MainController::class,
        'payolution_apiloglist' => ListController::class,
        'payolution_apilogdetails' => DetailsController::class,
        'payolution_install' => InstallController::class,
        'payolution_jslibrary' => JsLibraryController::class,
    ],
    'extend' => [
        ViewConfig::class => ViewConfigController::class,
        OrderOverview::class => OverviewController::class,
        OrderMain::class => \TopConcepts\Payolution\Module\Controller\Admin\Order\MainController::class,
        OrderArticle::class => OrderArticleController::class,
        CountryList::class => CountryListModel::class,
        \OxidEsales\Eshop\Application\Model\Country::class => CountryModel::class,
        \OxidEsales\Eshop\Application\Model\Order::class => OrderModel::class,
        \OxidEsales\Eshop\Application\Model\Payment::class => PaymentModel::class,
        PaymentList::class => PaymentListModel::class,
        PaymentGateway::class => PaymentGatewayModel::class,
        \OxidEsales\Eshop\Application\Model\OrderArticle::class => OrderArticleModel::class,
        OrderAddress::class => OrderAddressController::class,
        Email::class => \TopConcepts\Payolution\Module\Core\Email::class,
        ShopControl::class => \TopConcepts\Payolution\Module\Core\ShopControl::class,
        OrderController::class => \TopConcepts\Payolution\Module\Controller\OrderController::class,
    ],
    'templates' => [
        // backend tpl
        'payolution_regional_settings.tpl' => 'tc/payolution/views/admin/tpl/payolution_regional_settings.tpl',
        'payolution_expert.tpl' => 'tc/payolution/views/admin/tpl/payolution_expert.tpl',
        'payolution_config.tpl' => 'tc/payolution/views/admin/tpl/payolution_config.tpl',
        'payolution_orders.tpl' => 'tc/payolution/views/admin/tpl/payolution_orders.tpl',
        'payolution_apilog.tpl' => 'tc/payolution/views/admin/tpl/payolution_apilog.tpl',
        'payolution_apiloglist.tpl' => 'tc/payolution/views/admin/tpl/payolution_apiloglist.tpl',
        'payolution_apilogdetails.tpl' => 'tc/payolution/views/admin/tpl/payolution_apilogdetails.tpl',
        'payolution_install.tpl' => 'tc/payolution/views/admin/tpl/payolution_install.tpl',
        'payolution_empty.tpl' => 'tc/payolution/views/admin/tpl/payolution_empty.tpl',
        'payolution_jslibrary.tpl' => 'tc/payolution/views/admin/tpl/payolution_jslibrary.tpl',
        'email/html/payolution_order_pdf_email.tpl' => 'tc/payolution/views/admin/tpl/email/html/payolution_order_pdf_email.tpl',
        'email/plain/payolution_order_pdf_email.tpl' => 'tc/payolution/views/admin/tpl/email/plain/payolution_order_pdf_email.tpl',

        // frontend tplF
        'inc/select_payment_element.tpl' => 'tc/payolution/views/blocks/page/checkout/inc/select_payment_element.tpl',
        'inc/select_payment_element_flow.tpl' => 'tc/payolution/views/blocks/page/checkout/inc/select_payment_element_flow.tpl',
        'inc/select_payment_installment.tpl' => 'tc/payolution/views/blocks/page/checkout/inc/select_payment_installment.tpl',
        'invoice_b2b_select_payment_flow.tpl' => 'tc/payolution/views/blocks/page/checkout/invoice_b2b_select_payment_flow.tpl',
    ],
    'blocks' => [
        [
            'template' => 'layout/footer.tpl',
            'block' => 'footer_main',
            'file' => '/views/blocks/layout/footer_main.tpl'
        ],
        [
            'template' => 'layout/base.tpl',
            'block' => 'base_style',
            'file' => '/views/blocks/layout/base_head.tpl'
        ],
        [
            'template' => 'layout/base.tpl',
            'block' => 'base_js',
            'file' => '/views/blocks/layout/base_body.tpl'
        ],
        [
            'template' => 'page/checkout/thankyou.tpl',
            'block' => 'checkout_thankyou_info',
            'file' => '/views/blocks/page/checkout/checkout_thankyou_info.tpl'
        ],
        [
            'template' => 'page/checkout/payment.tpl',
            'block' => 'select_payment',
            'file' => '/views/blocks/page/checkout/select_payment.tpl'
        ],
        [
            'template' => 'page/checkout/payment.tpl',
            'block' => 'checkout_payment_errors',
            'file' => '/views/blocks/page/checkout/checkout_payment_errors.tpl'
        ],
        [
            'template' => 'page/checkout/inc/basketcontents.tpl',
            'block' => 'checkout_basketcontents_grandtotal',
            'file' => '/views/blocks/page/checkout/inc/checkout_basketcontents_grandtotal.tpl'
        ],
        [
            'template' => 'widget/product/listitem_infogrid.tpl',
            'block' => 'widget_product_listitem_infogrid_price_value',
            'file' => '/views/blocks/widget/product/widget_product_listitem_infogrid_price_value.tpl'
        ],
        [
            'template' => 'widget/product/listitem_line.tpl',
            'block' => 'widget_product_listitem_line_price_value',
            'file' => '/views/blocks/widget/product/widget_product_listitem_line_price_value.tpl'
        ],
        [
            'template' => 'page/details/inc/productmain.tpl',
            'block' => 'details_productmain_tobasket',
            'file' => '/views/blocks/page/details/inc/payo_checkout_button.tpl'
        ],
        [
            'template' => 'page/checkout/order.tpl',
            'block' => 'shippingAndPayment',
            'file' => '/views/blocks/page/checkout/shippingAndPayment.tpl'
        ],
        [
            'template' => 'widget/product/listitem_grid.tpl',
            'block' => 'widget_product_listitem_grid_price_value',
            'file' => '/views/blocks/widget/product/widget_product_listitem_grid_price_value.tpl'
        ],
        [
            'template' => 'widget/product/boxproducts.tpl',
            'block' => 'widget_product_boxproduct_price',
            'theme' => 'azure',
            'file' => '/views/blocks/widget/product/widget_product_boxproduct_price.tpl'
        ],
        [
            'template' => 'widget/product/boxproduct.tpl',
            'block' => 'widget_product_boxproduct_price',
            'theme' => 'azure',
            'file' => '/views/blocks/widget/product/widget_product_boxproduct_price.tpl'
        ],
    ],
    'settings' => [
        ['group' => 'main', 'name' => 'aPayolutionInsBankDataRequired', 'type' => 'arr', 'value' => ['a7c40f631fc920687.20179984']], // Germany
        ['group' => 'main', 'name' => 'blPayolutionAllowOtherShipAddr', 'type' => 'str', 'value' => '0'],
        ['group' => 'main', 'name' => 'iPayolutionServerMode', 'type' => 'str', 'value' => '0'], // Test server
        ['group' => 'main', 'name' => 'sPayolutionInvoicePdfEmailAddess', 'type' => 'str'],
        ['group' => 'main', 'name' => 'sPayolutionSender', 'type' => 'str'],
        ['group' => 'main', 'name' => 'sPayolutionLogin', 'type' => 'str'],
        ['group' => 'main', 'name' => 'sPayolutionPassword', 'type' => 'str'],
        ['group' => 'main', 'name' => 'sPayolutionChannelInvoiceB2C', 'type' => 'str'],
        ['group' => 'main', 'name' => 'sPayolutionChannelInvoiceB2B', 'type' => 'str'],
        ['group' => 'main', 'name' => 'sPayolutionChannelInstallment', 'type' => 'str'],
        ['group' => 'main', 'name' => 'sPayolutionChannelPreCheck', 'type' => 'str'],
        ['group' => 'main', 'name' => 'sPayolutionChannelDD', 'type' => 'str'],
        ['group' => 'main', 'name' => 'sPayolutionChannelCL', 'type' => 'str'],
        ['group' => 'main', 'name' => 'sPayolutionPasswordCL', 'type' => 'str'],
        ['group' => 'main', 'name' => 'fPayolutionMinInstallment', 'type' =>'str', 'value' => '200'],
        ['group' => 'main', 'name' => 'fPayolutionMaxInstallment', 'type' =>'str', 'value' => '999999'],
        ['group' => 'main', 'name' => 'bPayolutionShowPriceOnDetails', 'type' => 'str', 'value' => '1'],
        ['group' => 'main', 'name' => 'bPayolutionShowPriceOnCategory', 'type' => 'str', 'value' => '1'],
        ['group' => 'main', 'name' => 'bPayolutionShowPriceOnHomePage', 'type' => 'str', 'value' => '1'],
        ['group' => 'main', 'name' => 'bPayolutionShowPriceOnBasket', 'type' => 'str', 'value' => '1'],
    ]
];
