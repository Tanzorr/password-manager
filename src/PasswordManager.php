<?php

namespace App;


use App\Model\Password;
use DomainException;
use Exception;
use JetBrains\PhpStorm\NoReturn;

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
                $this->getChosenAction($action);
            } catch (DomainException $error) {
                $this->io->writeln("====================");
                $this->io->writeln("[ERROR]{$error->getMessage()}");
                $this->io->writeln("====================");
            }
        }
    }

    private function showMenu(): void
    {
        $this->io->writeln("===============");
        $this->io->writeln("Menu actions:");
        $this->io->writeln("[s] Show password");
        $this->io->writeln("[a] Add password");
        $this->io->writeln("[d] Delete password");
        $this->io->writeln("[c] Change password");
        $this->io->writeln("[l] List all passwords names");
        $this->io->writeln("[q] Exit");
        $this->io->writeln("===============");
    }

    /**
     * @throws Exception;
     */
    private function getChosenAction(string $action): void
    {
        match ($action) {
            "l" => $this->showAllPasswords(),
            "a" => $this->addPassword(),
            "s" => $this->showPassword(),
            "d" => $this->deletePassword(),
            "c" => $this->changePassword(),
            "q" => $this->logout(),
            default => $this->io->writeln("[ERROR]Unknown action"),
        };
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
