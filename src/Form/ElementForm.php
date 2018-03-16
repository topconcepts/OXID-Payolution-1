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
namespace Payolution\Form;

/**
 * Class ElementForm
 * @package Payolution\Form
 */
class ElementForm
{
    /**
     * Element of type input key
     */
    const INPUT = 'input';
    /**
     * Element of date input key
     */
    const DATE = 'date';
    /**
     * Element of checkbox input key
     */
    const CHECKBOX = 'checkbox';
    /**
     * Element of button input key
     */
    const BUTTON = 'button';
    /**
     * Element of type birthday key
     */
    const BIRTHDAY = 'birthday';
    /**
     * Element of type phone key
     */
    const PHONE = 'phone';
    /**
     * Element of type hidden key
     */
    const HIDDEN = 'hidden';

    /**
     * @var string
     */
    protected $_type;
    /**
     * @var string
     */
    protected $_title;
    /**
     * @var string
     */
    protected $_name;
    /**
     * @var null|string
     */
    protected $_value;
    /**
     * @var null|string
     */
    protected $_help;
    /**
     * @var string
     */
    protected $_className;
    /**
     * @var array
     */
    protected $_helpArgs = [];
    /**
     * @var bool
     */
    protected $_required = true;

    /**
     * @var string
     */
    protected $_errorText;

    /**
     * @var array
     */
    protected $_dataValues;

    /**
     * @param string      $type
     * @param string      $title
     * @param string      $name
     * @param null|string $value
     * @param null|string $help
     * @param bool        $required
     * @param null        $errorText
     * @param array       $dataValues
     */
    public function __construct(
      $type,
      $title,
      $name,
      $value = null,
      $help = null,
      $required = true,
      $errorText = null,
      $dataValues = []
    ) {
        $this->_type     = $type;
        $this->_title    = $title;
        $this->_name     = $name;
        $this->_value    = $value;
        $this->_help     = $help;
        $this->_required = $required;
        $this->_errorText = $errorText;
        $this->_dataValues = $dataValues;
    }

    /**
     * @param string $type
     * @param string $title
     * @param string $name
     * @param null|string $value
     * @param null|string $help
     * @param bool $required
     * @param null $errorText
     * @param array $dataValues
     *
     * @return ElementForm
     */
    public static function create(
      $type,
      $title,
      $name,
      $value = null,
      $help = null,
      $required = true,
      $errorText = null,
      $dataValues = []
    ) {
        return oxNew(ElementForm::class, $type, strtoupper($title),
          $name, $value, $help, $required, $errorText, $dataValues);
    }


    /**
     * @return string
     */
    public function type()
    {
        return $this->_type;
    }

    /**
     * @return string
     */
    public function title()
    {
        return 'PAYOLUTION_'.$this->_title;
    }

    /**
     * @return string
     */
    public function name()
    {
        return $this->_name;
    }

    /**
     * @return null|string
     */
    public function help()
    {
        return $this->_help;
    }

    /**
     * @return string|null
     */
    public function value()
    {
        return $this->_value;
    }

    /**
     * @param $value
     *
     * @return $this
     */
    public function setValue($value)
    {
        $this->_value = $value;

        if($this->type() == 'birthday') {
            $this->setBirthdayValue($value);
        }
        
        return $this;
    }

    /**
     * @param string $value
     */
    public function setBirthdayValue($value)
    {
        /** @var BindHelper $helper */
        $helper = oxNew(BindHelper::class);
        $this->_value = $helper->formatBirthday($value);
    }
    
    /**
     * @return string
     */
    public function className()
    {
        return $this->_className;
    }

    /**
     * @param $value
     *
     * @return ElementForm
     */
    public function setClassName($value)
    {
        $this->_className = $value;

        return $this;
    }

    /**
     * @return bool
     */
    public function required()
    {
        return $this->_required;
    }

    /**
     * @return null|string
     */
    public function errorText()
    {
        return $this->_errorText;
    }

    /**
     * @param bool $value
     *
     * @return ElementForm
     */
    public function setRequired($value)
    {
        $this->_required = $value ? true : false;

        return $this;
    }

    /**
     * @param $dataValues
     * @return $this
     */
    public function setDataValues($dataValues)
    {
        $this->_dataValues = $dataValues;
        return $this;
    }
    

    /**
     * @return string
     */
    public function dataValues()
    {
        return json_encode($this->_dataValues);
    }

    /**
     * @return array
     */
    public function helpArgs()
    {
        return $this->_helpArgs;
    }

    /**
     * @param string $arg
     *
     * @return ElementForm
     */
    public function addHelpArg($arg)
    {
        $this->_helpArgs[] = $arg;
        return $this;
    }
}
