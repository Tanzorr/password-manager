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

    private array $cache = [];

    private static ?self $instance = null;

    public function __construct()
    {
        if(self::$instance !== null){
            throw new \RuntimeException('Container was already intialized');
        }
        self::$instance = $this;
    }


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
            if ($parameter->getType()->isBuiltin()) {
                if (!isset($this->parameters[$parameter->getName()])) {
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

        if (!is_array($parameters)) {
            throw new \LogicException("Invalid parameters configuration in file {$serviceFilesPath}");
        }

        foreach ($parameters as $key => $value) {
            $this->setParameter($key, $value);
        }
    }

    public static function getInstance(): self
    {
        if(self::$instance === null) {
            return new self();
        }

        return self::$instance;
    }
}
