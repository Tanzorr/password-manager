<?php

namespace App;


use App\Model\Password;
use DomainException;
use Exception;
use JetBrains\PhpStorm\NoReturn;
use PhpSchool\CliMenu\Builder\CliMenuBuilder;
use PhpSchool\CliMenu\Exception\InvalidTerminalException;

class PasswordManager
{
    public function __construct(
        private InputOutput        $io,
        private AskHelper          $askHelper)
    {
    }

    public function run(): void
    {
        $this->io->writeln("Welcome to Password Manager");

        while (true) {
            try {
                $this->showMenu();
                $action = $this->io->expect("Choose action: ");
                passthru('clear');
            } catch (DomainException $error) {
                $this->io->writeln("====================");
                $this->io->writeln("[ERROR]{$error->getMessage()}");
                $this->io->writeln("====================");
            }
        }
    }

    /**
     * @throws InvalidTerminalException
     */
    private function showMenu(): void
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
        $attributes = [];

        $attributes['name'] = $this->askHelper->askPasswordName();
        $attributes['value'] = $this->askHelper->askPasswordValue();

        Password::create($attributes);
    }

    /**
     * @throws Exception
     */
    private function showPassword(): void
    {
        $passwordName = $this->askHelper->askPasswordName();
        $password = Password::find($passwordName);
        $hash = $password->hashedPassword;

        $this->io->writeln($password->value);
        $this->io->writeln($hash);
    }

    /**
     * @throws Exception
     */
    private function deletePassword(): void
    {
        $passwordName = $this->askHelper->askPasswordName();
        if (Password::delete($passwordName)) {
            $this->io->writeln("$passwordName Password deleted.");
        }
    }

    /**
     * @throws Exception
     */
    private function changePassword(): void
    {
        $attributes = [];
        $attributes['name'] = $this->askHelper->askPasswordName();
        $attributes['value'] = $this->askHelper->askPasswordValue();

        Password::update($attributes);
        $this->io->writeln($attributes['name'] . "Password changed.");
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
