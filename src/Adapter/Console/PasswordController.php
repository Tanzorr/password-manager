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
    public function addPassword(): void
    {
        Password::create([
            'name' => $this->askHelper->askPasswordName(),
            'value' => $this->askHelper->askPasswordValue()
        ]);

        Vault::update([
            'name' => $this->config->get('activeVault'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        $this->showMenu();
    }

    /**
     * @throws Exception
     */
    private function showPassword(): void
    {
        $this->io->writeln(Password::find($this->askHelper->askPasswordName())->value);
    }

    public function displayPassword(Password $password, String $vault, VaultController $vaultController)
    {
        $this->io->writeln("Password:".$password->name. "In Vault:".$vault);

        $menuBuilder = (new CliMenuBuilder())->setTitle('Password Menu actions:');

        $menuBuilder->addItem("Password:".$password->name. " In Vault:".$vault, function (){});
        $menuBuilder->addItem("********", function (){});

        $menuBuilder->addItem("========", function (){});
        $menuBuilder->addItem("Show password", $this->showPassword(...));
        $menuBuilder->addItem("Edit password", $this->changePassword(...));
        $menuBuilder->addItem("Delete password", $this->deletePassword(...));
        $menuBuilder->addItem("back", fn()=> $vaultController->selectVaultItem($vault));

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
    private function deletePassword(): void
    {
        if (Password::delete($passwordName = $this->askHelper->askPasswordName())) {
            $this->io->writeln("$passwordName Password deleted.");
            Vault::update([
                'name' => $this->config->get('activeVault'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }
        $this->showMenu();
    }

    /**
     * @throws Exception
     */
    private function changePassword(): void
    {
        Password::update([
            'name' => $this->askHelper->askPasswordName(),
            'value' => $this->askHelper->askPasswordValue()
        ]);

        Vault::update([
            'name' => $this->config->get('activeVault'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        $this->showMenu();
    }

    public function getAllPasswords():array
    {
      return  Password::findAll();
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
