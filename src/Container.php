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

        return $this->cache[$className] ??= $this->createInstance($reflection, $className);
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
        return class_exists($key)
            ? $this->cache[$key] ??= $this->build($key)
            : ($this->parameters[$key]
            ?? throw new LogicException("No value found for parameter {$key}"));
    }

    /**
     * @throws \Exception
     */
    public function load(string $serviceFilesPath): void
    {

        $serviceYaml = Yaml::parseFile((new FileLocator(dirname($serviceFilesPath)))->locate($serviceFilesPath));
        $this->binds = array_merge($this->binds, $serviceYaml['binds'] ?? []);
        $parameters = $serviceYaml['parameters'] ?? [];

        foreach ($parameters ?? [] as $key => $value) {
            $this->setParameter($key, $value);
        }

        if (!is_array($parameters)) {
            throw new \LogicException("Invalid parameters configuration in file {$serviceFilesPath}");
        }
    }

    private function getBind(string $className): string
    {
        $classNameArray = explode("\\", $className);
        return $this->binds[end($classNameArray)] ?? $className;
    }

    /**
     * @param array $parameters
     * @param string $className
     * @return array
     * @throws ReflectionException
     */
    private function getDependencies(array $parameters, string $className): array
    {
      return array_map(fn($parameter) => $this->getDependency($parameter, $className), $parameters);
    }

    /**
     * @throws ReflectionException
     */
    private function getDependency($parameter, string $className): mixed
    {
        $parameterType = $parameter->getType();
        $parameterName = $parameter->getName();

        if ($parameterType->isBuiltin()) {
          return $this->parameters[$parameterName] ?? throw new LogicException("No value found for parameter {$parameterName}");
        }

       return $this->build($parameterType->getName()) ?? throw new LogicException("Cannot autowire {$parameterName} argument of {$className} class. Please specify argument type.");
    }

    /**
     * @throws ReflectionException
     */
    private function createInstance(\ReflectionClass $reflection, string $className): object
    {
       $constructor = $reflection->getConstructor();

       if($constructor === null || empty($constructor->getParameters())) {
           return $reflection->newInstance();
       }

       return $reflection->newInstanceArgs($this->getDependencies($constructor->getParameters(), $className));
    }
}