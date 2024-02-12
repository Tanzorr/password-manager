<?php

use JetBrains\PhpStorm\NoReturn;

class PasswordManager
{
    private InputOutput $io;
    private Store $store;

    private AskHelper $askHelper;

    public function __construct(InputOutput $io, Store $store, AskHelper $askHelper)
    {
        $this->io = $io;
        $this->store = $store;
        $this->askHelper = $askHelper;
    }

    public function run(): void
    {
       $this->io->writeln("Welcome to Password Manager");

        while (true) {
            $this->showMenu();
            $action = $this->io->expect("Choose action: ");
            passthru('clear');
            $this->getChosenAction($action);
        }
    }

    private function showMenu(): void
    {
        echo str_repeat('=',20);
        echo "Menu actions: \n";
        echo "[s] Show password\n";
        echo "[a] Add password\n";
        echo "[d] Delete password\n";
        echo "[c] Change password\n";
        echo "[l] List all passwords names\n";
        echo "[q] Exit\n";
        echo str_repeat('=',20).PHP_EOL;
    }

    private function getChosenAction(string $action): void
    {
        match ($action) {
            "l" => $this->store->showAllPasswords(),
            "a" => $this->addPassword(),
            "s" => $this->showPassword(),
            "d" => $this->deletePassword(),
            "c" => $this->changePassword(),
            "q" => $this->logout(),
            default => $this->io->writeln("[ERROR]Unknown action"),
        };
    }

    private function addPassword(): void
    {
        $passwordName = $this->askHelper->askPasswordName();
        $passwordValue = $this->askHelper->askPasswordValue();

        $this->store->addPassword($passwordName, $passwordValue);
    }

    private function showPassword(): void
    {
        $passwordName = $this->askHelper->askPasswordName();
        $passwordValue = $this->store->getPassword($passwordName);

        $this->io->writeln($passwordValue);
    }

    private function deletePassword(): void
    {
        $passwordName = $this->askHelper->askPasswordName();
        $this->store->deletePassword($passwordName);
    }

    private function changePassword(): void
    {
        $passwordName = $this->askHelper->askPasswordName();
        $passwordValue = $this->askHelper->askPasswordValue();

        $this->store->changePassword($passwordName, $passwordValue);
    }

    #[NoReturn] private function logout(): void
    {
        exit();
    }
}
