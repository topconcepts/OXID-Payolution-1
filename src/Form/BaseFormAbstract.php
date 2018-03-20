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
namespace TopConcepts\Payolution\Form;

use TopConcepts\Payolution\Order\OrderContext;

/**
 * Class BaseFormAbstract
 * @package TopConcepts\Payolution\Form
 */
abstract class BaseFormAbstract
{
    /**
     * @var OrderContext
     */
    private $_orderingContext;

    /**
     * @var ElementForm[]
     */
    private $elements = array();

    /**
     * @param OrderContext $context
     * @param null $bindParams
     */
    public function __construct(OrderContext $context, $bindParams = null)
    {
        $this->_orderingContext = $context;
        $this->init();
        $this->bindContext($context);
        $this->bind($bindParams ? $bindParams : array());
    }

    /**
     * @return array|ElementForm[]
     */
    public function getElements()
    {
        return $this->elements;
    }

    /**
     * @param $name
     *
     * @return ElementForm
     */
    public function getElement($name)
    {
        return $this->get($name);
    }

    /**
     * @return OrderContext
     */
    public function orderingContext()
    {
        return $this->_orderingContext;
    }

    /**
     * Method initializes a form: here you must add fields to the form.
     */
    abstract protected function init();

    /**
     * This method then context is binded to form. here you must implement
     * logic for form field population. Override point.
     *
     * @param OrderContext $context
     */
    protected function bindContext(OrderContext $context){}

    /**
     * @param $name
     *
     * @return ElementForm|null
     */
    protected function get($name)
    {
        return isset($this->elements[$name]) ? $this->elements[$name] : null;
    }

    /**
     * @param ElementForm $element
     *
     * @return ElementForm return the same element which you've
     *                                 passed
     */
    protected function add(ElementForm $element)
    {
        $this->elements[$element->name()] = $element;

        return $element;
    }


    /**
     * @param $bindParams
     */
    private function bind($bindParams)
    {
        foreach ($this->getElements() as $element) {
            $location = array_filter(preg_split('/\[|\]\[|\]/', $element->name()));
            $value    = $this->lookupArrayValue($bindParams, $location);

            if ($value !== null) {
                $element->setValue($value);
            }
        }
    }


    /**
     * @param $array
     * @param $location
     *
     * @return null|mixed
     */
    private function lookupArrayValue($array, $location)
    {
        $p = $array;
        foreach ($location as $part) {
            if (isset($p[$part])) {
                $p = $p[$part];
            } else {
                return null;
            }
        }

        return $p;
    }

}
