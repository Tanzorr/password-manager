<?php

namespace App\Adapter\Console;

use App\Domain\Model\Password;
use App\Domain\Model\Vault;
use Exception;
use Illuminate\Contracts\Config\Repository;

class PasswordController
{
    public function __construct(
        private Repository  $config
    )
    {
    }

    /**
     * @throws Exception
     */
    public function addPassword(string $passwordName, string $passwordValue): void
    {
        Password::create([
            'name' => $passwordName,
            'value' => $passwordValue
        ]);

        Vault::update([
            'name' => $this->config->get('activeVault'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }


    /**
     * @throws Exception
     */
    public function showPassword($passwordName): string
    {
        return Password::find($passwordName)->value;
    }
    public function getAllPasswords(): array
    {
        $result = Password::findAll();

        return $result;
    }

    /**
     * @throws Exception
     */
    public function deletePassword(String $passwordName): bool
    {
        if (Password::delete($passwordName)) {
            Vault::update([
                'name' => $this->config->get('activeVault'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }

        return  true;
    }

    /**
     * @throws Exception
     */
    public function changePassword(string $passwordName, string $passwordValue): void
    {
        Password::update([
            'name' => $passwordName,
            'value' => $passwordValue
        ]);

        Vault::update([
            'name' => $this->config->get('activeVault'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }
}
