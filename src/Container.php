<?php

namespace App;

use LogicException;
use ReflectionException;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Yaml\Yaml;


class Container
{
    /**
     * @var array<string, object|int|array>
     */

    private array $parameters = [];

    private array $binds = [];

    private array $cache = [];

    private static ?self $instance = null;

    public function __construct()
    {
        if (self::$instance !== null) {
            throw new LogicException("Cannot create another instance of the container");
        }
        self::$instance = $this;
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }


    /**
     * @throws ReflectionException
     */
    public function build(string $className): object
    {
        $className = $this->getBind($className);
        $reflection = new \ReflectionClass($className);
        $constructor = $reflection->getConstructor();

        if (isset($this->cache[$className])) {
            return $this->cache[$className];
        }

        if ($constructor === null || empty($constructor->getParameters())) {
            return $this->cache[$className] = $reflection->newInstance();
        }

        $dependencies = $this->getDependencies($constructor->getParameters(), $className);

        return $this->cache[$className] = $reflection->newInstanceArgs($dependencies);
    }

    public function setParameter(string $key, string|int|array $value): void
    {
        $this->parameters[$key] = $value;
    }

    /**
     * @throws ReflectionException
     */
    public function get(string $key): mixed
    {
        if (class_exists($key)) {
            $this->cache[$key] ??= $this->build($key);

            return $this->cache[$key];
        }

        if ($this->parameters[$key]) {
            return $this->parameters[$key];
        }

        throw new LogicException("No value found for parameter {$key}");
    }

    /**
     * @throws \Exception
     */
    public function load(string $serviceFilesPath): void
    {
        $fileLocator = new FileLocator(dirname($serviceFilesPath));
        $serviceYaml = Yaml::parseFile($fileLocator->locate($serviceFilesPath));
        $parameters = $serviceYaml['parameters'] ?? [];
        $binds = $serviceYaml['binds'] ?? [];
        $this->binds = array_merge($this->binds, $binds);

        if (!is_array($parameters)) {
            throw new \LogicException("Invalid parameters configuration in file {$serviceFilesPath}");
        }

        foreach ($parameters as $key => $value) {
            $this->setParameter($key, $value);
        }
    }

    private function getBind(string $className): string
    {
        $classNameArray = explode("\\", $className);
        $normalizedClassName = end($classNameArray);

        if (isset($this->binds[$normalizedClassName])) {
            $className = $this->binds[$normalizedClassName];
        }

        return $className;
    }

    /**
     * @param array $parameters
     * @param string $className
     * @return array
     * @throws ReflectionException
     */
    private function getDependencies(array $parameters, string $className): array
    {
        return array_map(function ($parameter) use ($className) {
            $parameterType = $parameter->getType();
            $parameterName = $parameter->getName();

            if ($parameterType->isBuiltin()) {
                if (!isset($this->parameters[$parameterName])) {
                    throw new LogicException("No value found for parameter {$parameterName}");
                }
                return $this->parameters[$parameterName];
            }

            $name = $parameterType->getName();
            if ($name === null) {
                throw new LogicException("Cannot autowire {$parameterName} argument of {$className} class. Please specify argument type.");
            }
            return $this->build($name);
        }, $parameters);
    }
}