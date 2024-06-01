<?php

namespace App\Core\Model;

use Illuminate\Container\Container;

/*
 * @method static find()
 * method static static create(array $attributes)
 * @method static bool update(array $attributes)
 * @method static bool delete(int|string $id)
 * @method static static[]findAll()
 */
abstract class Model
{
    public function __construct(private array $attributes = [])
    {
    }

    public function __get(string $name): mixed
    {
        $method = 'get' . ucfirst($name);
        if (method_exists($this, $method)) {
            return $this->$method();
        }

        if (!isset($this->attributes[$name])) {
            throw new \LogicException("Attribute $name not found" . static::class, 1);
        }
        return $this->attributes[$name];
    }

    public function __set(string $name, mixed $value): void
    {
        if (!isset($this->attributes[$name])) {
            // self vs static
            throw new \LogicException("Attribute {$name} not found in class " . static::class, 1);
        }
        $this->attributes[$name] = $value;
    }

    /**
     * @throws \ReflectionException
     */
    public function __call(string $name, array $arguments): mixed
    {
        $segments = explode("\\", static::class);
        $modelName = end($segments);

        $container = Container::getInstance();
        $modelRepository = $container->get("App\\Repository\\{$modelName}Repository");

        return $modelRepository->$name(...$arguments);
    }


    public static function __callStatic(string $name, array $arguments): mixed
    {
        $newModel = new static();

        return $newModel->$name(...$arguments);
    }
}

