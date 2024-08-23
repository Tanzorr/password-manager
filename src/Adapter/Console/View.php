<?php

namespace App\Adapter\Console;

use App\AskHelper;
use App\Core\Console\InputOutput;
use App\Domain\Model\Password;
use DomainException;
use PhpSchool\CliMenu\Builder\CliMenuBuilder;
use PhpSchool\CliMenu\CliMenu;

class View
{
    public function __construct(
        private VaultController    $vaultController,
        private PasswordController $passwordController,
        private InputOutput        $io,
        private AskHelper          $askHelper
    )
    {
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
        $vaults = $this->vaultController->gatAllVaults();

        $menuBuilder = (new CliMenuBuilder())->setTitle('Menu actions:');

        $menuBuilder->addItem("Add vault", fn() => $this->addVault());

        $menuBuilder->addLineBreak("");
        $menuBuilder->addStaticItem("List of vaults");
        $menuBuilder->addLineBreak("=");

        $menuBuilder->addSubMenu("Vaults", function ($submenuBuilder) use ($vaults) {
            array_walk($vaults, function ($vault) use ($submenuBuilder) {
                $submenuBuilder->addItem($vault, fn() => $this->selectVault($vault));
            });
        });

        $menuBuilder->build()->open();
    }

    public function selectVault(string $vaultName): void
    {
        $this->io->writeln("Selected vault: $vaultName");
        $this->vaultController->setVaultConfig($vaultName);
        $vaultEncryptorKey = $this->vaultController->config->get("encryptionKey");

        if (!$vaultEncryptorKey) {
            $this->setEncryptionKey();
        }

        $passwords = $this->passwordController->getAllPasswords();

        $menuBuilder = (new CliMenuBuilder())->setTitle('Password Menu actions: in ' . $vaultName);

        $menuBuilder->addSubMenu("Actions", function ($submenuBuilder) use ($vaultName) {
            $submenuBuilder->addItem("edit Vault", fn() => $this->editVaultName($vaultName));
            $submenuBuilder->addItem("add password", fn() => $this->addPassword($vaultName));
            $submenuBuilder->addItem("Delete vault", fn() => $this->vaultController->deleteVault($vaultName));
        });

        $menuBuilder->addSubMenu("Passwords", function ($menuBuilder) use ($passwords, $vaultName) {
            array_walk($passwords, function ($password) use ($menuBuilder, $vaultName) {
                $menuBuilder->addSubMenu($password->name, fn() => $this->displayPassword($password, $vaultName));
            });
        });
        $menuBuilder->build()->open();
    }

    public function addVault(): void
    {
        $vaultName = $this->askHelper->askVaultName();
        $this->vaultController->addVault($vaultName);

        $this->io->writeln("Vault $vaultName created successfully");
    }

    public function editVaultName(string $vaultName): void
    {
        $this->io->writeln("Edit vault: $vaultName");
        $newVaultName = $this->io->expect("Enter new vault name for: $vaultName");

        $this->vaultController->editVaultName($vaultName, $newVaultName);
    }

    public function deleteVault(string $vaultName): void
    {
        if ($this->vaultController->deleteVault($vaultName)) {
            $this->io->writeln("Vault $vaultName deleted successfully");
        } else {
            $this->io->writeln("Vault $vaultName not found");
        }
    }

    private function addPassword(string $vaultName): void
    {
        $passwordName = $this->askHelper->askPasswordName();
        $passwordValue = $this->askHelper->askPasswordValue();

        $this->passwordController->addPassword($passwordName, $passwordValue);
        $this->io->writeln("Password $passwordName added to vault $vaultName");
    }

    private function displayPassword(Password $password, string $vault): void
    {
        $this->io->writeln("Password:" . $password->name . "In Vault:" . $vault);

        $menuBuilder = (new CliMenuBuilder())->setTitle('Password Menu actions:');

        $menuBuilder->addItem("Password:" . $password->name . " In Vault:" . $vault, function () {
        });
        $menuBuilder->addItem("********", function () {
        });

        $menuBuilder->addItem("========", function () {
        });
        $menuBuilder->addItem("Show password", fn() => $this->showPassword($password->name));
        $menuBuilder->addItem("Edit password", fn() => $this->changePassword($password->name));
        $menuBuilder->addItem("Delete password", fn() => $this->deletePassword($password->name));

        $menuBuilder->build()->open();
    }

    private function showPassword($passwordName): void
    {
        $this->io->writeln("Password: $passwordName");
        $this->io->writeln($this->passwordController->showPassword($passwordName));
    }

    private function changePassword(string $passwordName): void
    {
        $this->passwordController->changePassword($passwordName, $this->askHelper->askPasswordValue());
        $this->io->writeln("$passwordName Password updated.");
    }

    private function deletePassword(string $passwordName): void
    {
        if (!$this->passwordController->deletePassword($passwordName)) {
            $this->io->writeln("Password $passwordName not found");
            return;
        }
        $this->io->writeln("$passwordName Password deleted.");
    }

    public function setEncryptionKey(): void
    {
        $encryptionKey = $this->io->expect("Enter encryption name: ");
        $this->vaultController->setEncryptionKey($encryptionKey);
    }
}