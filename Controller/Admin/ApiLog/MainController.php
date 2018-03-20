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
namespace TopConcepts\Payolution\Module\Controller\Admin\ApiLog;

use OxidEsales\EshopCommunity\Application\Controller\Admin\AdminController;

/**
 * Admin payolution logs manager.
 * Returns template, that arranges two other templates ("payolution_apilog.tpl")
 *
 * Class MainController
 * @package TopConcepts\Payolution\Module\Controllers\Admin\ApiLog
 */
class MainController extends AdminController
{
    /**
     * Current class template name.
     * @var string
     */
    protected $_sThisTemplate = 'payolution_apilog.tpl';
}
