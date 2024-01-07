<?php

class Store
{
    private string $passwordsFile = 'passwords.json';
    private function readPasswordsFile(): array
    {
        $passwords = @file_get_contents($this->passwordsFile);

        if ($passwords === false) {
            return [];
        }

        return json_decode($passwords, true) ?? [];
    }


    public function setPassword($passwordName, $passwordValue): void
    {
        $passwords = $this->readPasswordsFile();
        $passwords[$passwordName] = $passwordValue;
        $passwords = json_encode($passwords);
        file_put_contents($this->passwordsFile, $passwords);
    }

    public function getPassword($passwordName)
    {
        $passwords = $this->readPasswordsFile();
        return $passwords[$passwordName];
    }

    public function deletePassword($passwordName): void
    {
        $passwords = $this->readPasswordsFile();
        unset($passwords[$passwordName]);
        $passwords = json_encode($passwords);
        file_put_contents($this->passwordsFile, $passwords);
    }

    public function showAllPasswords(): void
    {
        $passwords = $this->readPasswordsFile();
        if(count($passwords) === 0) {
            echo "No passwords found.\n";
            return;
        }

        foreach ($passwords as $key => $value) {
            echo "Password name: " . $key. "\n";
        }
    }

    public function replacePassword($passwordName, $passwordValue): void
    {
        $passwords = $this->readPasswordsFile();
        $passwords[$passwordName] = $passwordValue;
        $passwords = json_encode($passwords);
        file_put_contents($this->passwordsFile, $passwords);
    }
}