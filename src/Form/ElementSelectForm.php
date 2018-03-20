<?php
/**
 * Copyright 2017 Payolution GmbH
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

/**
 * Class ElementSelectForm
 * @package TopConcepts\Payolution\Form
 */
class ElementSelectForm extends ElementForm
{
    const TYPE = 'select';

    /**
     * ElementSelectForm constructor.
     * @param $type
     * @param $title
     * @param $name
     * @param null $value
     * @param null $help
     * @param bool $required
     * @param null $errorText
     * @param array $dataValues
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
        parent::__construct($type, $title, $name, $value, $help, $required, $errorText, $dataValues);
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
     * @return ElementSelectForm
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
        return oxNew(ElementSelectForm::class, $type, strtoupper($title),
            $name, $value, $help, $required, $errorText, $dataValues);
    }
    
    /**
     * @param string|array $value
     *
     * @return $this
     */
    public function setValue($value)
    {
        if (!is_array($value)) {
            $this->_value[$value]->setSelected(true);
        } else {
            $this->_value = $value;
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getSelectedValue()
    {
        $value = '';
        /** @var SelectedItemForm $selectItem */
        foreach ($this->value() as $selectItem) {
            if ($selectItem->isSelected()) {
                return $selectItem->getValue();
            }
        }

        return $value;
    }
}
