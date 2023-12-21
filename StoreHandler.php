<?php

require_once 'Store.php';

class StoreHandler
{
    private $store;
    public function __construct(Store $store)
    {
        $this->store = $store;
    }

    public function inputPassword()
    {
        $passwordName = readline("Enter password name: ");
        $passwordValue = readline("Enter password value: ");
        $this->store->setPassword($passwordName, $passwordValue);
    }

    public function showAllPasswords()
    {
        $this->store->showAllPasswords();
    }

    public function getPassword()
    {
        $passwordName = readline("Enter password name: ");
        $passwordValue = $this->store->getPassword($passwordName);
        echo "Password value: " . $passwordValue;
    }

    public function deletePassword()
    {
        $passwordName = readline("Enter password name: ");
        $this->store->deletePassword($passwordName);
    }

    public function replacePassword()
    {
        $passwordName = readline("Enter password name: ");
        $passwordValue = readline("Enter password value: ");
        $this->store->replacePassword($passwordName, $passwordValue);
    }
}