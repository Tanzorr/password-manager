<?php

namespace App\Adapter\Console;

use App\Core\Console\InputOutput;
use App\Domain\Model\Vault;
use DomainException;
use Illuminate\Contracts\Config\Repository;
use PhpSchool\CliMenu\Builder\CliMenuBuilder;
use PhpSchool\CliMenu\CliMenu;
use PhpSchool\CliMenu\Exception\InvalidTerminalException;

class VaultController
{
    protected $config;

    public function __construct(
        Repository                 $config,
        private PasswordController $passwordManager,
        private InputOutput        $io,
    )
    {
        $this->config = $config;
    }

    public function run(): void
    {
        $this->io->writeln("Welcome to Password Manager");

        while (true) {
            try {
                $this->showVaultsMenu();
            } catch (DomainException $error) {
                $this->io->writeln("====================");
                $this->io->writeln("[ERROR]{$error->getMessage()}");
                $this->io->writeln("====================");
            }
        }
    }

    public function showVaultsMenu(): void
    {
        $this->config->set('encryptionKey', '');
        $vaults = array_diff(Vault::findAll(), ['.', '..']);
        if (count($vaults) === 0) {
            throw new DomainException("No vaults found");
        }

        $menuBuilder = (new CliMenuBuilder())->setTitle('Menu actions:');

        $menuBuilder->addItem("Add vault", fn() => $this->addVault());
        $menuBuilder->addItem("Quit", function () {
        });
        $menuBuilder->addItem("========", function () {
        });
        $menuBuilder->addItem("Select vaults:", function () {
        });

        array_walk($vaults, function ($vault) use ($menuBuilder) {
            $menuBuilder->addItem($vault, fn(CliMenu $menu) => $this->selectVaultItem($vault));
        });

        $menuBuilder->build()->open();
    }

    /**
     * @throws InvalidTerminalException
     */
    public function selectVaultItem(string $vault): void
    {
        $this->io->writeln("Selected vault: $vault");
        $this->setVaultConfig($vault);
        $vaultEncryptorKey = $this->config->get("encryptionKey");

        if (!$vaultEncryptorKey) {
            $this->setEncryptionKey();
        }

        $passwords = $this->passwordManager->getAllPasswords();

        if (count($passwords) === 0) {
            $this->io->writeln("<< No passwords found >>");
        }

        $menuBuilder = (new CliMenuBuilder())->setTitle('Password Menu actions: in '.$vault);

        $menuBuilder->addItem("edit Vault", fn() => $this->editVaultName($vault));
        $menuBuilder->addItem("add password", fn() => $this->passwordManager->addPassword($this, $vault));
        $menuBuilder->addItem("========", function () {
        });
        $menuBuilder->addItem("Select password", function () {
        });

        array_walk($passwords, function ($password) use ($menuBuilder, $vault) {
            $menuBuilder->addItem($password->name, fn() => $this->passwordManager->displayPassword($password, $vault, $this));
        });

        $menuBuilder->addItem("Back", fn() => $this->showVaultsMenu());
        $menuBuilder->build()->open();
    }

    private function setVaultConfig(string $vault): void
    {
        $this->config->set('storagePath', 'vaults/' . $vault);
        $this->config->set('activeVault', $vault);
    }

    private function editVaultName(string $vaultName): void
    {
        $this->io->writeln("Edit vault: $vaultName");
        $newVaultName = $this->io->expect("Enter new vault name for: $vaultName");

         if(Vault::updateVaultName($vaultName, $newVaultName)){
             $this->selectVaultItem($newVaultName);
         };
    }

    private function setEncryptionKey(): void
    {
        $encryptionKey = $this->io->expect("Enter encryption name: ");
        if ($encryptionKey === '') {
            $this->io->writeln("Encryption name is empty.");
            exit;
        }
        $this->config->set('encryptionKey', $encryptionKey);
    }

    public function addVault(): void
    {
        Vault::create([
            'name' => $vaultName = $this->io->expect("Enter vault name: "),
            'created_at' => date('Y-m-d H:i:s')
        ]);

        $this->io->writeln("Vault $vaultName created successfully");
    }

    public function deleteVault(): void
    {
        if (Vault::delete($vaultName = $this->io->expect("Enter vault name: "))) {
            $this->io->writeln("Vault $vaultName deleted successfully");
        } else {
            $this->io->writeln("Vault $vaultName not found");
        }
    }
}