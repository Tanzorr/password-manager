<?php

namespace App;


use DomainException;
use Exception;
use JetBrains\PhpStorm\NoReturn;

class PasswordManager
{
    public function __construct(
        private InputOutput $io,
        private Store       $store,
        private AskHelper   $askHelper)
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
        $passwordName = $this->askHelper->askPasswordName();
        $passwordValue = $this->askHelper->askPasswordValue();

        $this->store->addPassword($passwordName, $passwordValue);
    }

    /**
     * @throws Exception
     */
    private function showPassword(): void
    {
        $passwordName = $this->askHelper->askPasswordName();
        $passwordValue = $this->store->getPassword($passwordName);

        $this->io->writeln($passwordValue);
    }

    /**
     * @throws Exception
     */
    private function deletePassword(): void
    {
        $passwordName = $this->askHelper->askPasswordName();
        $this->store->deletePassword($passwordName);

        $this->io->writeln("$passwordName Password deleted.");
    }

    /**
     * @throws Exception
     */
    private function changePassword(): void
    {
        $passwordName = $this->askHelper->askPasswordName();
        $passwordValue = $this->askHelper->askPasswordValue();

        $this->store->changePassword($passwordName, $passwordValue);
        $this->io->writeln("$passwordName Password changed.");
    }

    /**
     * @throws Exception
     */
    private function showAllPasswords(): void
    {
            $passwords = $this->store->getAllPasswords();
            $this->io->writeln("===============");
            if(count($passwords) === 0){
                $this->io->writeln("<< No passwords found >>");
            }
            foreach ($passwords as $key => $value) {
                $this->io->writeln("Password name: " . $key);
            }
            $this->io->writeln("===============");

    }

    #[NoReturn] private function logout(): void
    {
        exit();
    }
}
