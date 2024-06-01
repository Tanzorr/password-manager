<?php

namespace App\Model;

/**
 * @property string $name
 * @property string $value
 */
class Password extends Model
{

    public function getHashedPassword(): string
    {
        return password_hash($this->attributes['value'], PASSWORD_DEFAULT) ?? '';
    }

    /**
     * @prperty string $value
     * @prperty string $name
     */
    protected array $attributes = [
        'name' => null,
        'value' => null,
    ];
}