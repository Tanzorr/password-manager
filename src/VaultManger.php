<?php

namespace App;

use App\Model\Vault;
use Illuminate\Contracts\Config\Repository;
use JetBrains\PhpStorm\NoReturn;
use PhpSchool\CliMenu\Builder\CliMenuBuilder;
use PhpSchool\CliMenu\CliMenu;
use DomainException;
use PhpSchool\CliMenu\Exception\InvalidTerminalException;

class VaultManger
{
    public function __construct(
        private AskHelper       $askHelper,
        private Repository      $config,
        private PasswordManager $passwordManager,
        private InputOutput     $io,
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
            ->addItem("Select vault", $this->selectVault(...))
            ->addItem("Add vault", $this->addVault(...))
            ->addItem("Delete vault", $this->deleteVault(...))
            ->build();

        $menu->open();
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
            'name' => $this->askHelper->askVaultName(),
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    public function deleteVault(): void
    {
        Vault::delete($this->askHelper->askVaultName());
    }

    #[NoReturn] private function logout(): void
    {
        exit();
    }
}