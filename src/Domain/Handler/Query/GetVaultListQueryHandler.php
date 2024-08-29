<?php

namespace App\Domain\Handler\Query;

use App\Domain\Model\Vault;
use App\Domain\Query\GetVaultListQuery;

class GetVaultListQueryHandler
{
    public function handle(GetVaultListQuery $command)
    {
        return Vault::findAll();
    }
}
