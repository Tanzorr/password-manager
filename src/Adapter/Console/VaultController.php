<?php

namespace App\Adapter\Console;

use App\Domain\Model\Vault;
use DomainException;
use Illuminate\Contracts\Config\Repository;
use PhpSchool\CliMenu\Exception\InvalidTerminalException;

class VaultController
{
    private const VAULTS_PATH = 'vaults/';
    public $config;

    public function __construct(
        Repository                 $config,
    ) {
        $this->config = $config;
    }
    public function gatAllVaults(): array
    {
        $this->config->set('encryptionKey', '');
        $vaults = array_diff(Vault::findAll(), ['.', '..']);
        if (count($vaults) === 0) {
            throw new DomainException("No vaults found");
        }

        return $vaults;
    }

    /**
     * @throws InvalidTerminalException
     * */

    public function setVaultConfig(string $vault): void
    {
        $this->config->set('storagePath', self::VAULTS_PATH . $vault);
        $this->config->set('activeVault', $vault);
    }
    public function addVault($vaultName): bool
    {
        Vault::create([
            'name' => $vaultName,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        return true;
    }

    public function deleteVault(string $vaultName): bool
    {
       return Vault::delete($vaultName);
    }

    public function setEncryptionKey($encryptionKey): void
    {
        $this->config->set('encryptionKey', $encryptionKey);
    }

    public function editVaultName(string $vaultName, $newVaultName): void
    {
        Vault::updateVaultName($vaultName, $newVaultName);
    }
}

