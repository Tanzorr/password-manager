<?php

namespace App\Adapter\Console;

use App\Core\Console\InputOutput;
use App\Domain\Model\Vault;
use DomainException;
use Illuminate\Contracts\Config\Repository;
use JetBrains\PhpStorm\NoReturn;
use PhpSchool\CliMenu\Builder\CliMenuBuilder;
use PhpSchool\CliMenu\CliMenu;
use PhpSchool\CliMenu\Exception\InvalidTerminalException;

class VaultController
{
    public function __construct(
        private Repository         $config,
        private PasswordController $passwordManager,
        private InputOutput        $io,
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
       $menu = (new CliMenuBuilder())
                ->setTitle("Menu actions:")
                ->addItem("Add vault", fn() => $this->addVault())
                ->addItem("Delete vault", fn() => $this->deleteVault())
                ->addItem("Select vault", fn() => $this->selectVault())
                ->addItem("Logout", fn() => $this->logout())
                ->build();

            $menu->open();
    }

    private function setVaultConfig(string $vault): void
    {
        $this->config->set('storagePath', 'vaults/' . $vault);
        $this->config->set('activeVault',  $vault);
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
       if(Vault::delete($vaultName = $this->io->expect("Enter vault name: "))){
           $this->io->writeln("Vault $vaultName deleted successfully");
       } else {
           $this->io->writeln("Vault $vaultName not found");
       }
    }

    /**
     * @throws InvalidTerminalException
     */
    public function selectVault(): void
    {
        $vaults = array_diff(Vault::findAll(), ['.', '..']);
        if(count($vaults) === 0){
            throw new DomainException("No vaults found");
        }
        $menuBuilder = (new CliMenuBuilder())->setTitle('Select vaults:');

        array_walk($vaults, function ($vault) use ($menuBuilder) {
            $menuBuilder->addItem($vault, fn(CliMenu $menu) => $this->selectVaultItem($vault, $menu));
        });

        $menuBuilder->build()->open();
    }

    /**
     * @throws InvalidTerminalException
     */
    private function selectVaultItem(string $vault, CliMenu $menu): void
    {
        $this->io->writeln("Selected vault: $vault");
        $this->setVaultConfig($vault);
        $this->setEncryptionKey();
        $this->passwordManager->showMenu();
        $menu->close();
    }
    #[NoReturn] private function logout(): void
    {
        exit();
    }
}