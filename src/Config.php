<?php

namespace App;

use Illuminate\Contracts\Config\Repository as ConfigContract;
class Config implements ConfigContract
{
    public function __construct(protected array $parameters = [])
    {
    }


    public function has($key):bool
    {
        return isset($this->parameters[$key]);
    }

    public function get($key, $default = null):string
    {
        return $this->parameters[$key] ?? $default ?? new \LogicException("Key {$key} not found");
    }

    public function all():array
    {
        return $this->parameters;
    }

    public function set($key, $value = null):self
    {
        $this->parameters[$key] = $value;
        return $this;
    }

    public function prepend($key, $value)
    {
        // TODO: Implement prepend() method.
    }

    public function push($key, $value)
    {
        // TODO: Implement push() method.
    }
}