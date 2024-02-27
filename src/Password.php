<?php

namespace App;

class Password
{
    public array $attributes;

    public function __construct(array $attributes) {
        $this->attributes = $attributes;
    }

    public function getValue(): string
    {
        return $this->attributes['value'];
    }
}