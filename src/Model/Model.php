<?php

namespace App\Model;

use App\Container;
use App\Repository\PasswordRepository;

/**
* @method static static find()
* @method static static create(array $attributes)
* @method static bool update(array $attributes)
* @method static bool delete(int|string $id)
* @method static static[] findAll()
**/
abstract class Model
{
    public function __construct(protected array $attributes = [])
    {
    }

    // Model attributes control
    // if not exists, or is not accessible, then magic method is executed
    public function __get(string $name): mixed
    {
        // implement here virutal attributes using method_exists("get".ucfirst($name)."Attribute");
        // if(method_Exists(...)) { return $this->{$methodnName}(); }
        // see accessors and mutators

        if(!isset($this->attributes[$name])) {
            // self vs static
            throw new \LogicException("Attribute {$name} not found in class " . static::class, 1);
        }

        return $this->attributes[$name];
    }

    public function __set(string $name, mixed $value): void
    {
        if(!isset($this->attributes[$name])) {
            // self vs static
            throw new \LogicException("Attribute {$name} not found in class " . static::class, 1);
        }
        $this->attributes[$name] = $value;
    }

    // QueryBuilder

    public function __call(string $name, array $arguments): mixed
    {
        $segments = explode("\\", static::class);
        $modelName = end($segments); // App\\Model\\Password -> Password

        $container = Container::getInstance();
        $modelRepository = $container->get("App\\Repository\\{$modelName}Repository");

        return $modelRepository->{$name}(...$arguments);
    }

    public static function __callStatic(string $name, array $arguments): mixed
    {
        $newModel = (new static());

        return $newModel->{$name}(...$arguments);
    }
}
