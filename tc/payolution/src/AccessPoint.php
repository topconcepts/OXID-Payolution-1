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

/**
 * Class Payolution_AccessPoint is used as main access point to Payolution
 * module from all oxid system This class ensures that PayolutionModule object
 * will be only one in system. Oxid doesn't have service container so its
 * impossible to force singleton in any other way.
 *
 * Class AccessPoint
 * @package Payolution
 */
class AccessPoint
{
    /**
     * @var PayolutionModule
     */
    private static $module;

    /**
     * @return PayolutionModule
     */
    private static function init()
    {
        $services = oxNew(PayolutionServices::class);
        $module   = oxNew(PayolutionModule::class, $services);

        return $module;
    }

    /**
     * @return PayolutionModule
     */
    public function getModule()
    {
        return self::$module ? self::$module : (self::$module = self::init());
    }
}
