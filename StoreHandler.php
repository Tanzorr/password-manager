<?php

require_once 'Store.php';

class StoreHandler
{
    private mixed $store;
    private mixed $passwordEncryptor;

    public function __construct($storeClass,  $passwordEncryptorClass)
    {
        $this->store = new $storeClass();
        $this->passwordEncryptor = new $passwordEncryptorClass(ENCRYPTION_KEY); // Ensure ENCRYPTION_KEY is defined
    }

    public function showMessage(string $message): void
    {
        echo $message . "\n";
    }


    public function inputActions(): string
    {
        return readline("Enter your choice action: ");
    }

    public function inputPassword(): void
    {
        $passwordName = trim(readline("Enter password name: "));
        $this->showEmptyFieldError('password name',$passwordName);
        $passwordValue = trim(readline("Enter password value: "));
        $this->showEmptyFieldError('password value',$passwordValue);

        $encryptedPassword = $this->passwordEncryptor->encryptPassword($passwordValue);
        $this->store->setPassword($passwordName, $encryptedPassword);
    }

    public function showAllPasswords(): void
    {
        $this->store->showAllPasswords();
    }

    public function getPassword(): void
    {
        $passwordName = trim(readline("Enter password name: "));
        $this->showEmptyFieldError('password name',$passwordName);

        $passwordValue = $this->store->getPassword($passwordName);
        if ($passwordValue !== null) {
            $decryptedPassword = $this->passwordEncryptor->decryptPassword($passwordValue);
            echo "Password value: " . $decryptedPassword . "\n";
        } else {
            echo "Password not found.\n";
        }
    }

    public function deletePassword(): void
    {
        $passwordName = readline("Enter password name: ");
        $this->showEmptyFieldError('password name',$passwordName);
        $this->store->deletePassword($passwordName);
    }

    public function replacePassword(): void
    {
        $passwordName = trim(readline("Enter password name: "));
        $this->showEmptyFieldError('password name',$passwordName);
        $passwordValue = trim(readline("Enter password value: "));
        $this->showEmptyFieldError('password value',$passwordValue);

        $encryptedPassword = $this->passwordEncryptor->encryptPassword($passwordValue);
        $this->store->replacePassword($passwordName, $encryptedPassword);
    }

    private function showEmptyFieldError($fieldName, $fieldValue): void
    {
        if($fieldValue === '') {
            $this->showMessage("$fieldName can't be empty.");
            return;
        }
    }
}
