<?php

namespace App\Adapter\Console;

use App\AskHelper;
use App\Core\Console\InputOutput;
use DomainException;
use PhpSchool\CliMenu\Action\GoBackAction;
use PhpSchool\CliMenu\Builder\CliMenuBuilder;

class VaultView
{
    public function __construct(
        private VaultController $vaultController,
        private AskHelper       $askHelper,
        private InputOutput     $io,
        private PasswordController $passwordController,
        private PasswordView $passwordView

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
                $this->askHelper->displayText("====================");
                $this->askHelper->displayText("[ERROR]{$error->getMessage()}");
                $this->askHelper->displayText("====================");
            }
        }
    }

    private function showVaultsMenu(): void
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

    private function selectVault(string $vaultName): void
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
            $submenuBuilder->addItem("add password", fn() => $this->passwordView->addPassword($vaultName));
            $submenuBuilder->addItem("Delete vault", fn() => $this->deleteVault($vaultName));
        });

        $menuBuilder->addSubMenu("Passwords", function ($menuBuilder) use ($passwords, $vaultName) {
            array_walk($passwords, function ($password) use ($menuBuilder, $vaultName) {
                $menuBuilder->addItem($password->name, fn() => $this->passwordView->displayPassword($password, $vaultName));
            });
        });

        $menuBuilder->build()->open();
    }

    private function addVault(): void
    {
        $vaultName = $this->askHelper->askVaultName();
        $this->vaultController->addVault($vaultName);

        $this->io->writeln("Vault $vaultName created successfully");
    }

    private function editVaultName(string $vaultName): void
    {
        $this->io->writeln("Edit vault: $vaultName");
        $newVaultName = $this->io->askText("Enter new vault name for: $vaultName");

        $this->vaultController->editVaultName($vaultName, $newVaultName);
    }

    private function deleteVault(string $vaultName): void
    {
        if ($this->vaultController->deleteVault($vaultName)) {
            $this->io->writeln("Vault $vaultName deleted successfully");
        } else {
            $this->io->writeln("Vault $vaultName not found");
        }
    }

    private function setEncryptionKey(): void
    {
        $encryptionKey = $this->io->askText("Enter encryption name: ");
        $this->vaultController->setEncryptionKey($encryptionKey);
    }
}