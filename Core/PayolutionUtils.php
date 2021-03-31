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

namespace TopConcepts\Payolution\Core;


use oxexception;
use OxidEsales\Eshop\Core\Registry;

/**
 * Class PayolutionUtils
 * @package TopConcepts\Payolution\Core
 */
class PayolutionUtils
{

    /**
     * @param $name
     * @return mixed
     */
    public static function getShopConfVar($name)
    {
        $config = Registry::getConfig();
        $shopId = $config->getShopId();

        return $config->getShopConfVar($name, $shopId, 'module:payolution');
    }

    /**
     * @param $e oxException
     */
    public static function logException(oxException $e) {
        if (method_exists(Registry::class, 'getLogger')) {
            Registry::getLogger()->error('PAYOLUTION ' . $e->getMessage(), [$e]);
        } else {
            $e->debugOut();
        }
    }

    public static function log($level, $message, $context = []) {
        if (method_exists(Registry::class, 'getLogger')) {
            Registry::getLogger()->log($level, 'PAYOLUTION ' . $message, $context);
        } else {
            $targetLogFile = 'oxideshop.log';
            // eshop 6.0 log wrapper
            $oConfig = Registry::getConfig();
            $iDebug = $oConfig->getConfigParam('iDebug');
            $level =  strtoupper($level);
            $context = json_encode($context);
            if ($level !== 'ERROR' && $iDebug === 0) {
                return; // don't log anything besides errors in production mode
            }
            $date = (new \DateTime())->format('Y-m-d H:i:s');
            Registry::getUtils()->writeToLog(
                "[$date] OXID Logger.$level: PAYOLUTION $message $context\n",
                $targetLogFile
            );
        }
    }
}
