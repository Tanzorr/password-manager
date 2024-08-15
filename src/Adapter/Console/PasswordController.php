<?php

namespace App\Adapter\Console;

use App\AskHelper;
use App\Core\Console\InputOutput;
use App\Domain\Model\Password;
use App\Domain\Model\Vault;
use App\NoReturn;
use Exception;
use Illuminate\Contracts\Config\Repository;
use PhpSchool\CliMenu\Builder\CliMenuBuilder;
use PhpSchool\CliMenu\Exception\InvalidTerminalException;

class PasswordController
{
    public function __construct(
        private InputOutput $io,
        private AskHelper   $askHelper,
        private Repository  $config
    )
    {
    }

    /**
     * @throws InvalidTerminalException
     */
    public function showMenu(): void
    {
        $menu = (new CliMenuBuilder())
            ->setTitle("Menu actions:")
            ->addItem("Show password", $this->showPassword(...))
            ->addItem("Add password", $this->addPassword(...))
            ->addItem("Delete password", $this->deletePassword(...))
            ->addItem("Change password", $this->changePassword(...))
            ->addItem("List all passwords names", $this->showAllPasswords(...))
            ->addItem("logout", $this->logout(...))
            ->build();

        $menu->open();
    }

    /**
     * @throws Exception
     */
    public function addPassword(VaultController $vaultController, string $vault): void
    {
        Password::create([
            'name' => $this->askHelper->askPasswordName(),
            'value' => $this->askHelper->askPasswordValue()
        ]);

        Vault::update([
            'name' => $this->config->get('activeVault'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        $vaultController->selectVaultItem($vault);
    }

    /**
     * @throws Exception
     */
    private function showPassword($passwordName): void
    {
        $this->io->writeln(Password::find($passwordName)->value);
    }

    public function displayPassword(Password $password, string $vault, VaultController $vaultController)
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
        $menuBuilder->addItem("Delete password", fn() => $this->deletePassword($password->name, $vault, $vaultController));
        $menuBuilder->addItem("back", fn() => $vaultController->selectVaultItem($vault));

        $menuBuilder->build()->open();
    }


    public function askForPasswordValue(): string
    {
        return $this->io->expect("Enter password value:");
    }


    public function askForPasswordName(): string
    {
        return $this->io->expect("Enter password name:");
    }

    /**
     * @throws Exception
     */
    private function deletePassword(String $passwordName, string $vaultName, VaultController $vaultController): void
    {
        if (Password::delete($passwordName)) {
            $this->io->writeln("$passwordName Password deleted.");
            Vault::update([
                'name' => $this->config->get('activeVault'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }

        $vaultController->selectVaultItem($vaultName);
    }

    /**
     * @throws Exception
     */
    private function changePassword(String $passwordName): void
    {
        Password::update([
            'name' => $passwordName,
            'value' => $this->askHelper->askPasswordValue()
        ]);

        Vault::update([
            'name' => $this->config->get('activeVault'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }

    public function getAllPasswords(): array
    {
        return Password::findAll();
    }

    /**
     * @throws Exception
     */
    public function showAllPasswords(): void
    {
        $passwords = Password::findAll();
        $this->io->writeln("===============");

        if (count($passwords) === 0) {
            $this->io->writeln("<< No passwords found >>");
        }

        foreach ($passwords as $password) {
            $this->io->writeln("Password name: " . $password->name);
        }

        $this->io->writeln("===============");
    }

    #[NoReturn] private function logout(): void
    {
        exit();
    }
}
