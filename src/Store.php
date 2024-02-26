<?php

namespace App;

use Exception;

class Store implements RepositoryInterface
{
    public function __construct(
        private  FilesystemEncryptor $filesystemEncryptor,
        private  string              $storagePath = '',
    )
    {
    }

    /**
     * @throws Exception
     */
    public function addPassword(string $passwordName, string $passwordValue): bool
    {
        $passwords = $this->readPasswordsFile();
        $passwords[$passwordName] = $passwordValue;
        $this->filesystemEncryptor->put($this->storagePath, json_encode($passwords));

        return true;
    }

    /**
     * @throws Exception
     */
    public function getPassword(string $passwordName): string
    {
        $passwords = $this->readPasswordsFile();
        if (!array_key_exists($passwordName, $passwords)) {
            return "Password not found.";
        }

        $decryptPassword = $passwords[$passwordName];

        if ($decryptPassword) {
            return $decryptPassword;
        } else {
            return 'Password not found.';
        }
    }

    /**
     * @throws Exception
     */
    public function deletePassword(string $passwordName): void
    {
        if (!$this->isPasswordExist($passwordName)) {
            $passwords = $this->readPasswordsFile();
            unset($passwords[$passwordName]);
            $this->filesystemEncryptor->put($this->storagePath, json_encode($passwords));
        } else {
            throw new Exception("Password not found.");
        }
    }

    /**
     * @throws Exception
     */
    public function getAllPasswords(): array
    {
        return $this->readPasswordsFile();
    }

    /**
     * @throws Exception
     */
    private function readPasswordsFile(): array
    {
        if (!$this->filesystemEncryptor->exists($this->storagePath)) {
            $this->filesystemEncryptor->put($this->storagePath, json_encode([]));
            return [];
        }

        $passwords = $this->filesystemEncryptor->get($this->storagePath);

        if ($passwords === '') {
            throw new Exception('Access denied');
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
            $this->filesystemEncryptor->put($this->storagePath, json_encode($passwords));
        }
    }

    /**
     * @throws Exception
     */
    private function isPasswordExist(string $passwordName): bool
    {
        $passwords = $this->readPasswordsFile();
        if (!array_key_exists($passwordName, $passwords)) {
            throw new Exception("Password not found.");
        }

        return true;
    }

    /**
     * @throws Exception
     */
    public function create(array $attributes): object
    {
        if (!$this->isPasswordExist($attributes['name'])) {
            $password = new Password($attributes);
        } else {
            throw new Exception("Password already exists.");
        }
        return $password;
    }

    public function update(int|string $id): bool
    {
        // TODO: Implement update() method.
    }

    /**
     * @throws Exception
     */
    public function delete(int|string $id): bool
    {
        if ($this->isPasswordExist($id)) {
            $this->deletePassword($id);
            return true;
        }
        return false;
    }

    /**
     * @throws Exception
     */
    public function find(int|string $id): ?object
    {
        return $this->readPasswordsFile()[$id];
    }

    /**
     * @throws Exception
     */
    public function all(): array
    {
        return $this->readPasswordsFile();
    }
}