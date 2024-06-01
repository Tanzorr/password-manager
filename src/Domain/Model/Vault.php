<?php

namespace App\Domain\Model;

use App\Core\Model\Model;

/**
 * @property string $name
 * @property string $path
 * @property string created_at
 * @property string updated_at
 */
class Vault extends Model
{
    protected array $attributes = [
      'name' => null,
      'path' => null,
      'created_at' => null,
      'updated_at' => null,
    ];
}

