<?php

namespace App;

use LogicException;
use RuntimeException;

class Container
{
    /**
     * @var array<string,string|int|array>
     */
    private array $parameters = [];

    private array $cache = [];

    /**
     * @throws \ReflectionException
     */
    public function build(string $className): object # меняем немного сигнатуру чтобы потом сделать переход на контейнер из ларавеле и ещё для того чтобы добавить больше функционала в текущий
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
            // добавим сюда поддержку биндинка параметров которые потом используются в конструкторах
            if($parameter->getType()->isBuiltin()) {
                if(!isset($this->parameters[$parameter->getName()])) {
                    throw new RuntimeException("There is no '{$parameter->getName()}' parameter that was binded to container.");
                }
                $dependencies[] = $this->parameters[$parameter->getName()];
                continue;
            }

            // Check if the parameter has a type hint
            $name = $parameter->getType()?->getName();

            // Handle the case where the type is not available
            if ($name === null) {
                throw new RuntimeException("Cannot autowire '{$parameter->getName()}' argument of '{$className}' class. Please specify argument type.");
            }
            $dependencies[] = $this->build($name);
        }

        return $reflection->newInstanceArgs($dependencies);
    }

    public function setParameter(string $key, string|int|array $value): void
    {
        $this->parameters[$key] = $value;
    }

    public function get(string $key): mixed
    {
        if (class_exists($key)) {
            // если в кэше уже есть наш обьект мы его переиспользуем, вот почему очень важно чтобы наши сервисы не хранили  никаких состояний, исключения могут быть только сервисы которые являются синглтонами
            $this->cache[$key] ??= $this->build($key);

            return $this->cache[$key];
        }
        if($this->parameters[$key]) {
            return $this->parameters[$key];
        }

        throw new LogicException("Cannot fetch from container any service/parameter under key '{$key}'.");
    }
}
