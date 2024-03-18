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
        if(!isset($this->attributes['value'])){
              throw new \LogicException("No hashed password " . static::class, 1);
       }

       return password_hash($this->attributes['value'], PASSWORD_DEFAULT);
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