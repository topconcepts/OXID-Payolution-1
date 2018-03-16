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
namespace Payolution\Module\Core;

use Payolution\Utils\JavascriptLibraryUtils;

/**
 * Class ShopControl
 * @see ShopControl
 * @package Payolution\Module\Core
 */
class ShopControl extends ShopControl_Parent
{
    /**
     * Update payolution js library if it is expired
     *
     * @return void
     */
    public function _runOnce()
    {
        parent::_runOnce();

        if ($this->isAdmin()) {
            return;
        }

        /** @var JavascriptLibraryUtils $jsLibrary */
        $jsLibrary = oxNew(JavascriptLibraryUtils::class);

        try {
            if ($jsLibrary->isExpired()) {
                $jsLibrary->updateWithLock();
            }
        } catch (\Exception $e) {
            // Do nothing if failed update. Will try to update later
        }
    }
}
