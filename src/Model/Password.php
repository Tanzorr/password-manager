<?php

namespace App\Model;


/**
 * @property string $value
 * @property string $name
 **/
class Password extends Model
{
    protected array $attributes = [
        'value' => null,
        'name' => null,
    ];

    // @see accessor, mutator
    // public function getHashedPassword(){
    //     // ....
    //     return "";
    // }
}
