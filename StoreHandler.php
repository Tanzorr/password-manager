<?php

require_once 'Store.php';

interface StoreHandlerInterface {
    public function inputActions(): string;
    public function getPassword(): void;
    public function inputPassword(): void;
    public function deletePassword(): void;
    public function replacePassword(): void;
    public function showAllPasswords(): void;
}

class StoreHandler implements StoreHandlerInterface
{
    private mixed $store;
    private mixed $passwordEncryptor;

    public function __construct($storeClass, $passwordEncryptorClass)
    {
        $this->store = new $storeClass();
        $this->passwordEncryptor = new $passwordEncryptorClass(ENCRYPTION_KEY); // Ensure ENCRYPTION_KEY is defined
    }

    public function inputActions(): string
    {
        return readline("Enter your choice action: ");
    }
    public function inputPassword(): void
    {
        $passwordName = $this->getField('password name');
        $passwordValue = $this->getField('password value');

        $encryptedPassword = $this->passwordEncryptor->encryptPassword($passwordValue);
        $this->store->setPassword($passwordName, $encryptedPassword);
    }

    public function showAllPasswords(): void
    {
        $this->store->showAllPasswords();
    }

    private function getField($fieldName)
    {
        $fieldValue = trim(readline("Enter $fieldName: "));
        if ($fieldValue === '') {
            echo "$fieldName can't be empty.\n";

            return $this->getField($fieldName);
        } else {
            return $fieldValue;
        }
    }

    public function getPassword(): void
    {
        $passwordName = $this->getField('password name');

        $passwordValue = $this->store->getPassword($passwordName);
        if ($passwordValue !== null) {
            $decryptedPassword = $this->passwordEncryptor->decryptPassword($passwordValue);

            echo "Password: $decryptedPassword\n";
        } else {
            echo "Password not found.\n";
        }
    }

    public function deletePassword(): void
    {
        $passwordName = $this->getField('password name');
        $passwordValue = $this->store->getPassword($passwordName);

        if ($passwordValue !== null) {
            $this->store->deletePassword($passwordName);
            echo "Password deleted.\n";
        } else {
            echo "Password not found.\n";
        }
    }

    public function replacePassword(): void
    {
        $passwordName = $this->getField('password name');
        $oldPasswordValue = $this->store->getPassword($passwordName);

        if ($oldPasswordValue === null) {
            echo "Password not found.\n";
            return;
        } else {
            $decryptedPassword = $this->passwordEncryptor->decryptPassword($oldPasswordValue);
            $passwordValue = $this->getField('password value');

            if ($decryptedPassword === $passwordValue) {
                echo "New password can't be the same as old password.\n";
                return;
            }
        }
        $encryptedPassword = $this->passwordEncryptor->encryptPassword($passwordValue);
        $this->store->replacePassword($passwordName, $encryptedPassword);

        echo "Password replaced.\n";
    }
}
