<?php

class Store
{
    private $passwordsFile = 'passwords.json';


    public function getPasswords()
    {
        $passwords = file_get_contents($this->passwordsFile);
        return json_decode($passwords, true);
    }

    public function setPassword($passwordName, $passwordValue)
    {
        $passwords = $this->getPasswords();
        $passwords[$passwordName] = $passwordValue;
        $passwords = json_encode($passwords);
        file_put_contents($this->passwordsFile, $passwords);
    }

    public function getPassword($passwordName)
    {
        $passwords = $this->getPasswords();
        return $passwords[$passwordName];
    }

    public function deletePassword($passwordName)
    {
        $passwords = $this->getPasswords();
        unset($passwords[$passwordName]);
        $passwords = json_encode($passwords);
        file_put_contents($this->passwordsFile, $passwords);
    }

    public function showAllPasswords()
    {
        $passwords = $this->getPasswords();
        foreach ($passwords as $key => $value) {
            echo "Password name: " . $key. "\n";
        }
    }

    public function replacePassword($passwordName, $passwordValue)
    {
        $passwords = $this->getPasswords();
        $passwords[$passwordName] = $passwordValue;
        $passwords = json_encode($passwords);
        file_put_contents($this->passwordsFile, $passwords);
    }
}