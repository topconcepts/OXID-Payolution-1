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
namespace Payolution\Module\Controller\Admin;

use OxidEsales\Eshop\Application\Controller\Admin\ShopConfiguration;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\UtilsView;
use Payolution\Module\Core\Exception\PayolutionException;
use Payolution\Utils\JavascriptLibraryUtils;

/**
 * Class JsLibraryController
 * @package Payolution\Module\Controllers\Admin
 */
class JsLibraryController extends ShopConfiguration
{
    /**
     * Current class template name.
     * 
     * @var string
     */
    protected $_sThisTemplate = 'payolution_jslibrary.tpl';

    /**
     * General template parameters for payolution module
     *
     * @var array
     */
    private $_payolutionTemplateParams = [];

    /**
     * Rendering Admin start page
     *
     * @return string
     */
    public function render()
    {
        // force shopid as parameter
        // Pass shop OXID so that shop object could be loaded
        $sShopOXID = $this->getConfig()->getShopId();
        $this->setEditObjectId($sShopOXID);

        parent::render();

        $this->setTemplateParam('sbLastUpdate', $this->fileExists() ? date('Y-m-d H:i', $this->getLastUpdateTime()) : false);

        return $this->_sThisTemplate;
    }

    /**
     * Initiate update process
     */
    public function update()
    {
        try {
            /** @var JavascriptLibraryUtils $jsLibrary */
            $jsLibrary = oxNew(JavascriptLibraryUtils::class);
            $jsLibrary->update();

            $this->setTemplateParam('bUpdated', true);
        } catch (PayolutionException $e) {
            $oEx = oxNew(PayolutionException::class);
            $oEx->setMessage($e->getMessage());
            Registry::get(UtilsView::class)->addErrorToDisplay($oEx);
        }
    }

    /**
     * Get Payolution module url with added path
     *
     * @param string $sPath
     * @return string
     */
    public function getPayolutionModuleUrl($sPath = '')
    {
        return $this->getConfig()->getConfigParam('sShopDir') . 'modules/tc/payolution/' . $sPath;
    }

    /**
     * Get path where Payolution installment JavaScript library files will be stored
     *
     * @return string
     */
    public function getPath()
    {
        return $this->getPayolutionModuleUrl('out/src/js/libs/payolution/');
    }

    /**
     * Get Payolution installment JavaScript library file name
     *
     * @return string
     */
    public function getFilename()
    {
        return 'payo-installment.js';
    }

    /**
     * Check if Payolution installment JavaScript library file is already downloaded and exists
     *
     * @return bool
     */
    private function fileExists()
    {
        return file_exists($this->getPath().$this->getFilename());
    }

    /**
     * Get Payolution installment JavaScript library last modification (last update) time as UNIX timestamp
     *
     * @return int
     */
    private function getLastUpdateTime()
    {
        return filectime($this->getPath().$this->getFilename());
    }

    /**
     * General template parameter setter
     *
     * @param string $name
     * @param $value
     */
    protected function setTemplateParam($name, $value)
    {
        $this->_payolutionTemplateParams[$name] = $value;
    }

    /**
     * General template parameter getter
     *
     * @param string $name
     * @return null
     */
    public function getTemplateParam($name)
    {
        if (isset($this->_payolutionTemplateParams[$name])) {
            return $this->_payolutionTemplateParams[$name];
        }

        return null;
    }
}
