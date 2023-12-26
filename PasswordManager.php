<?php

class PasswordManager
{
    private $storeHandler;

    public function __construct(string $storeHandlerClass, string $storeClass)
    {
        $this->storeHandler = new $storeHandlerClass(new $storeClass(), new PasswordEncryptor(ENCRYPTION_KEY));
    }

    public function run()
    {
        echo "Welcome to Password Manager!\n";

        $this->showMenu();

        while (true) {
            $this->getChosenPassword();
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

    private function getUnknownAction(): void
    {
        echo "Unknown action. Please try again\n";
        $this->showMenu();
    }

    private function getChosenPassword(): void
    {
        $selectedChoiceFromMenu = $this->storeHandler->inputActions();
        $this->processChoice($selectedChoiceFromMenu);
    }

    private function processChoice($choice): void
    {
        match ($choice) {
            'show' => $this->storeHandler->getPassword(),
            'add' => $this->storeHandler->inputPassword(),
            'delete' => $this->storeHandler->deletePassword(),
            'change' => $this->storeHandler->replacePassword(),
            'show all' => $this->storeHandler->showAllPasswords(),
            'q' => exit(),
            default => $this->getUnknownAction(),
        };
    }
}

// Path: PasswordEncryptor.php
