<?php

namespace App\Adapter\Console\Controller;

use App\Domain\Model\Password;
use App\Domain\Model\Vault;
use App\InputOutput;
use Exception;
use JetBrains\PhpStorm\NoReturn;
use Illuminate\Contracts\Config\Repository;
use PhpSchool\CliMenu\Builder\CliMenuBuilder;
use PhpSchool\CliMenu\Exception\InvalidTerminalException;

class PasswordController
{
    public function __construct(
        private InputOutput $io,
        private Repository  $config
    ) {
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
    private function addPassword(): void
    {
        Password::create([
            'name' => $this->askForPasswordName(),
            'value' => $this->askForPasswordValue()
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
        $password = Password::find($this->askForPasswordName());
        # FIXME: что произойдёт если пароля нет?
        $this->io->writeln($password->value);
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
        if (Password::delete($passwordName = $this->askForPasswordName())) {
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
            'name' => $this->askForPasswordName(),
            'value' => $this->askForPasswordValue(),
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
    private function showAllPasswords(): void
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
