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
namespace Payolution\Order;

/**
 * Class OrderStatus
 * @package Payolution\Order
 */
class OrderStatus
{
    /**
     * @return OrderStatus
     */
    public static function Unknown()
    {
        return self::_get('unknown');
    }

    /**
     * @return OrderStatus
     */
    public static function Prechecked()
    {
        return self::_get('prechecked');
    }

    /**
     * @return OrderStatus
     */
    public static function Created()
    {
        return self::_get('created');
    }

    /**
     * @return OrderStatus
     */
    public static function Cancelled()
    {
        return self::_get('cancelled');
    }

    /**
     * @return OrderStatus
     */
    public static function Shipped()
    {
        return self::_get('shipped');
    }

    /**
     * @return OrderStatus
     */
    public static function PartlyShipped()
    {
        return self::_get('partly_shipped');
    }

    /**
     * @return OrderStatus
     */
    public static function Refunded()
    {
        return self::_get('refunded');
    }

    /**
     * @return OrderStatus
     */
    public static function PartlyRefunded()
    {
        return self::_get('partly_refunded');
    }

    /**
     * @return OrderStatus
     */
    public static function Updated()
    {
        return self::_get('updated');
    }
    
    /**
     * @param $name
     *
     * @return OrderStatus
     */
    public static function fromString($name)
    {
        $enum = [
            'prechecked',
            'created',
            'cancelled',
            'shipped',
            'partly_shipped',
            'refunded',
            'partly_refunded',
            'updated'
        ];

        return in_array($name, $enum) ? self::_get($name) : self::Unknown();
    }

    /**
     * @var array
     */
    private static $instances = array();

    /**
     * @param $name
     *
     * @return OrderStatus
     */
    private static function _get($name)
    {
        if (!isset(self::$instances[$name])) {
            self::$instances[$name] = oxNew(self::class, $name);
        }

        return self::$instances[$name];
    }

    /**
     * @var string
     */
    private $_name;

    /**
     * @param string $name
     *
     * @deprecated do not use constructor directly! use static method instead!
     * @private
     */
    public function __construct($name)
    {
        $this->_name = $name;
    }

    /**
     * @return string
     */
    public function name()
    {
        return $this->_name;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->_name;
    }
}
