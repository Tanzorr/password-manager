<?php

namespace App;

use DomainException;
use Exception;

class Store
{
    public function __construct(
        // этот класс должен работать только с файлом, соответственно он не должен выводить никакого текста на экран, именно поэтому мы и убрали IO
        private readonly FilesystemEncryptor  $filesystem,
        protected string $storagePath
    ) {
    }

    /**
     * @throws Exception
     */
    public function addPassword(string $passwordName, string $passwordValue): bool
    {
        $passwords = $this->readPasswordsFile();
        $passwords[$passwordName] = $passwordValue;
        $this->filesystem->put($this->storagePath, json_encode($passwords));

        return true;
    }

    /**
     * @throws Exception
     */
    public function getPassword(string $passwordName): string
    {
        $passwords = $this->readPasswordsFile();
        $decryptPassword = $passwords[$passwordName];

        if (!$decryptPassword) {
            throw new DomainException("Password was not found.");
        }

        return $decryptPassword;
    }

    /**
     * @throws Exception
     */
    public function deletePassword(string $passwordName): void
    {
        $passwords = $this->readPasswordsFile();

        if (!array_key_exists($passwordName, $passwords)) {
            throw new DomainException("Password was not found.");
        }

        unset($passwords[$passwordName]);
        $this->filesystem->put($this->storagePath, json_encode($passwords));
    }

    /**
     * @throws Exception
     */
    public function getAllPasswords(): array
    {
        // если нам нечего показывать, то это не ошибка, просто список будет пустым
        return $this->readPasswordsFile();
    }

    /**
     * @throws Exception
     */
    private function readPasswordsFile(): array
    {
        if (!$this->filesystem->exists($this->storagePath)) {
            $this->filesystem->put($this->storagePath, json_encode([]));
            return []; // в случае если это первый пароль оно не должно ещё отваливаться
        }

        $passwords = $this->filesystem->get($this->storagePath);


        if ($passwords === '') {
            throw new DomainException('Access denied');
        }

        return json_decode($passwords, true);
    }

    /**
     * @throws Exception
     */
    public function changePassword(string $passwordName, string $newPasswordValue): void
    {
        $passwords = $this->readPasswordsFile();

        if ($this->isPasswordExist($passwordName)) {
            $passwords[$passwordName] = $newPasswordValue;
            $this->filesystem->put($this->storagePath, json_encode($passwords));
        }
    }

    /**
     * @throws Exception
     */
    private function isPasswordExist(string $passwordName): bool
    {
        $passwords = $this->readPasswordsFile();
        if (!array_key_exists($passwordName, $passwords)) {
            throw new DomainException("Password not found.");
        }

        return true;
    }
}
