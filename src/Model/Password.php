<?php

namespace App\Model;

class Password extends Model
{
    /**
     * @prperty string $value
     * @prperty string $name
     */
    protected array $attributes = [
        'name' => null,
        'value' => null,
    ];
}