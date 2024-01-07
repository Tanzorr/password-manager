<?php

class PasswordManager
{
    private $storeHandler;

    public function __construct(StoreHandler $storeHandler)
    {
        $this->storeHandler = $storeHandler;
    }

    public function run()
    {
        echo "Welcome to Password Manager!\n";

        while (true) {
            $this->showMenu();
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
            'q' => $this->exitApplication(),
            default => $this->getUnknownAction(),
        };
    }

    private function getUnknownAction(): void
    {
        echo "Unknown action. Please try again\n";
        $this->showMenu();
    }

    public function exitApplication(): void
    {
        echo "Exiting Password Manager. Goodbye!\n";
        exit;
    }

}
