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
namespace TopConcepts\Payolution\Utils;

/**
 * Class MiniDiUtils
 * @package TopConcepts\Payolution\Utils
 */
class MiniDiUtils
{
    /**
     * @var array
     */
    private $configuration;

    /**
     * MiniDiUtils constructor.
     * @param $configuration
     */
    public function __construct($configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * @param string $name
     *
     * @throws \RuntimeException
     * @return object
     */
    public function get($name)
    {
        if (!isset($this->configuration[$name])) {
            throw new \RuntimeException("Cannot find service which name is `{$name}`");
        }

        $serviceConfiguration = $this->configuration[$name];

        if (!isset($serviceConfiguration['instance'])) {

            if (!isset($serviceConfiguration['factory'])) {
                if (!class_exists($serviceConfiguration['class'])) {
                    throw new \RuntimeException("Service `{$name}` cannot be created. Class not found: " . $serviceConfiguration['class']);
                }
            }

            $instance = $this->createServiceInstance($name, $serviceConfiguration);

            if (!$instance) {
                throw new \RuntimeException("Cannot create a service `{$name}` for class `{$serviceConfiguration['class']}`. Reflection errors");
            }

            if (!@$serviceConfiguration['multi_instance']) {
                $this->configuration[$name]['instance'] = $instance;
            }

        } else {
            $instance = $this->configuration[$name]['instance'];
        }

        return $instance;
    }

    /**
     * @param string $name
     * @param array  $configuration
     *
     * @return object
     */
    private function createServiceInstance($name, $configuration)
    {
        $instance              = null;
        $constructorParameters = array();

        if (isset($configuration['arguments'])) {
            // * this could stuck in endless loop if configuration is wrong, need code to detect looping dependencies.
            foreach ($configuration['arguments'] as $serviceKey) {
                $constructorParameters [] = $this->get($serviceKey);
            }
        }

        if (isset($configuration['factory'])) {
            $instance = call_user_func_array($configuration['factory'], $constructorParameters);
        } elseif ($configuration['class']) {
            $reflectionClass = new \ReflectionClass($configuration['class']);

            $instance = (count($constructorParameters) > 0)
                ? $reflectionClass->newInstanceArgs($constructorParameters)
                : $reflectionClass->newInstance();
        }

        return $instance;
    }
}
