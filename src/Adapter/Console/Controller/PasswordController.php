<?php

namespace App\Adapter\Console\Controller;

use App\AskHelper;
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
        private AskHelper   $askHelper,
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
