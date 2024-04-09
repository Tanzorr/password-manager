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
        return md5($this->attributes['value']);
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
