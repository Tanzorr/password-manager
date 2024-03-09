<?php

namespace App;

class Password
{

    public function __construct(private array $attributes)
    {
    }

    public function getValue(): string
    {
        return $this->attributes['value'];
    }

    public function getName(): string
    {
        return $this->attributes['name'];
    }

}