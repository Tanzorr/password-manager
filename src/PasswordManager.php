<?php

class PasswordManager
{
    private $io;
    private $store;

    private $askHelper;

    private $auth;

    public function __construct(IO $io, Store $store, AskHelper $askHelper, Auth $auth)
    {
        $this->io = $io;
        $this->store = $store;
        $this->askHelper = $askHelper;
        $this->auth = $auth;
    }

    public function run()
    {
        echo "Welcome to Password Manager!\n";

        while (true) {
            $this->showMenu();
            $this->getChosenAction();
        }
    }

    private function showMenu(): void
    {
        echo "\nMenu actions: \n";
        echo "show. Show password\n";
        echo "add. Add password\n";
        echo "delete. Delete password\n";
        echo "change. Change password\n";
        echo "show all. Show all passwords names\n";
        echo "q. Exit\n";
    }

    private function getChosenAction(): void
    {
        $action = $this->io->expect("Choose action: ");

        match ($action) {
            "show all" => $this->store->showAllPasswords(),
            "add" => $this->addPassword(),
            "show" => $this->showPassword(),
            "delete" => $this->deletePassword(),
            "change" => $this->changePassword(),
            "q" => $this->logout(),
            default => $this->io->writeln("Unknown action"),
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
        $this->io->writeln($this->store->getPassword($passwordName));
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

    private function logout(): void
    {
        $this->auth->logout();
        exit();
    }
}
