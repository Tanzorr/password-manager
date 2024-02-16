<?php

namespace App;

class Container
{
    /**
     * @throws \ReflectionException
     */
    public function resolveClass($className)
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
            // Check if the parameter has a type hint
            $name = $parameter?->getType()?->getName();

            // Handle the case where the type is not available
            if ($name !== null) {
                $dependencies[] = $this->resolveClass($name);
            }
        }

        return $reflection->newInstanceArgs($dependencies);
    }
}