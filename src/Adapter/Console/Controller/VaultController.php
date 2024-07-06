<?php

namespace App\Adapter\Console\Controller;

use App\Core\Console\InputOutput;
use App\Domain\Model\Vault;
use App\Domain\Query\GetVaultListQuery;
use Illuminate\Contracts\Config\Repository;
use JetBrains\PhpStorm\NoReturn;
use League\Tactician\CommandBus;
use PhpSchool\CliMenu\Builder\CliMenuBuilder;
use PhpSchool\CliMenu\CliMenu;
use DomainException;
use PhpSchool\CliMenu\Exception\InvalidTerminalException;

class VaultController
{
    public function __construct(
        private Repository      $config,
        // тут така штука что вложеность одного "менеджера" в другой приводит к тому что их поведение отображения страдает
        // потому легче будет вынести часть логики их работы в отдельные сервисы  домена (domain),
        // тут мы потихонечку будем вводить такое понятие как DDD (domain driven design) и hexagonal architecture нам в этом поможет
        private PasswordController $passwordController,
        private InputOutput     $io,
        private CommandBus $bus
    ) {
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
        $vaults = $this->bus->handle(new GetVaultListQuery());

        $vaults = array_diff($vaults, ['.', '..']);
        if(count($vaults) === 0) {
            throw new DomainException("No vaults found");
        }
        $menuBuilder = (new CliMenuBuilder())->setTitle('Select vaults:');

        array_walk($vaults, function ($vault) use ($menuBuilder) {
            $menuBuilder->addItem($vault, fn (CliMenu $menu) => $this->selectVaultItem($vault, $menu));
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
        $this->passwordController->showMenu();
        $menu->close();
    }

    private function setVaultConfig(string $vault): void
    {
        $this->config->set('storagePath', 'vaults/' . $vault);
        $this->config->set('activeVault', $vault);
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
        $vault = Vault::create([
            'name' => $vaultName = $this->io->expect("Vault:"),
            'created_at' => date('Y-m-d H:i:s')
        ]);

        $this->inputOutput->writeln("Vault {$vaultName} created successfully");
    }

    public function deleteVault(): void
    {
        if(Vault::delete($id = $this->io->expect("Vault:"))) {
            $this->inputOutput->writeln('Vault' . $id . ' deleted successfully');
        } else {
            $this->inputOutput->writeln('Vault ' . $id . ' does not exist');

        }
    }

    #[NoReturn] private function logout(): void
    {
        exit();
    }
}
