<?php

require_once 'Store.php';

class StoreHandler
{
    private $store;
    private $passwordEncryptor;

    public function __construct($storeClass,  $passwordEncryptorClass)
    {
        $this->store = new $storeClass();
        $this->passwordEncryptor = new $passwordEncryptorClass(ENCRYPTION_KEY); // Ensure ENCRYPTION_KEY is defined
    }

    public function inputActions(): string
    {
        $action = readline("Enter your choice action: ");
        return $action;
    }

    public function inputPassword()
    {
        $passwordName = readline("Enter password name: ");
        $passwordValue = readline("Enter password value: ");
        $encryptedPassword = $this->passwordEncryptor->encryptPassword($passwordValue);
        $this->store->setPassword($passwordName, $encryptedPassword);
    }

    public function showAllPasswords()
    {
        $this->store->showAllPasswords();
    }

    public function getPassword()
    {
        $passwordName = readline("Enter password name: ");
        $passwordValue = $this->store->getPassword($passwordName);
        if ($passwordValue !== null) {
            $decryptedPassword = $this->passwordEncryptor->decryptPassword($passwordValue);
            echo "Password value: " . $decryptedPassword . "\n";
        } else {
            echo "Password not found.\n";
        }
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
