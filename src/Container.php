<?php

namespace App;

use LogicException;
use ReflectionException;

class Container
{
    /**
     * @var array<string, object|int|array>
     */

    private array $parameters = [];

    private array $cache = [];


    /**
     * @throws \ReflectionException
     */
    public function build(string $className): object
    {
        $reflection = new \ReflectionClass($className);
        $constructor = $reflection->getConstructor();

        if ($constructor === null) {
            return $reflection->newInstance();
        }

        $dependencies = [];
        $parameters = $constructor->getParameters();

        if (empty($parameters)) {
            return $reflection->newInstance();
        }

        foreach ($parameters as $parameter) {
            if($parameter->getType()->isBuiltin()){
                if(!isset($this->parameters[$parameter->getName()])){
                    throw new LogicException("No value found for parameter {$parameter->getName()}");
                }
                $dependencies[] = $this->parameters[$parameter->getName()];
                continue;
            }

            // Check if the parameter has a type hint
            $name = $parameter->getType()?->getName();

            // Handle the case where the type is not available
            if ($name === null) {
                throw new LogicException("Cannot autowire'
                 {$parameter->getName()}'
                 argument of {$className} class. Please specify argument type.");
            }
            $dependencies[] = $this->build($name);
        }

        return $reflection->newInstanceArgs($dependencies);
    }
    
    public function setParameter(string $key, string|int|array $value): void
    {
        $this->parameters[$key] = $value;
    }

    public function load(string $serviceFilesPath): void
    {

    }
}