<?php

class PasswordManager
{
    private $storeHandler;

    public function __construct(string $storeHandlerClass, string $storeClass)
    {
        $this->storeHandler = new $storeHandlerClass(new $storeClass());
    }

    public function run()
    {
        echo "Welcome to Password Manager!\n";

        $this->showMenu();

        while (true) {
            $this->getChosenPassword();
        }
    }

    private function showMenu()
    {
        echo "\nMenu actions: \n";
        echo "1. Show password\n";
        echo "2. Add password\n";
        echo "3. Delete password\n";
        echo "4. Change password\n";
        echo "5. Show all passwords names\n";
        echo "q. Exit\n";
    }

    private function getChosenPassword()
    {
        $selectedChoiceFromMenu = readline("Enter your choice action: ");
        $this->processChoice($selectedChoiceFromMenu);
    }

    private function processChoice($choice)
    {
        match ($choice) {
            '1' => $this->storeHandler->getPassword(),
            '2' => $this->storeHandler->inputPassword(),
            '3' => $this->storeHandler->deletePassword(),
            '4' => $this->storeHandler->replacePassword(),
            '5' => $this->storeHandler->showAllPasswords(),
            'q' => exit(),
            default => $this->showMenu(),
        };
    }
}

?>
