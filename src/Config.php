<?php

namespace App;

use Illuminate\Contracts\Config\Repository as ConfigContract;
class Config implements ConfigContract
{
    public function __construct(protected array $parameters = [])
    {
    }


    public function has($key)
    {
        // TODO: Implement has() method.
    }

    public function get($key, $default = null)
    {
        return $this->parameters[$key] ?? $default ?? new \LogicException("Key {$key} not found");
    }

    public function all()
    {
        // TODO: Implement all() method.
    }

    public function set($key, $value = null)
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