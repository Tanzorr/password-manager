<?php

namespace App\Repository;

use App\FilesystemEncryptor;
use App\Model\Password;
use Exception;

class PasswordRepository implements RepositoryInterface
{
    public function __construct(
        private FilesystemEncryptor $filesystemEncryptor,
        private string              $storagePath = '',
    )
    {
    }

    /**
     * @throws Exception
     */
    public function find(int|string $id): ?object
    {
        $password = $this->readPasswordsFile()[$id];

        if ($password) {
            return new Password(['name' => $id, 'value' => $password]);
        }
        return null;
    }

    /**
     * @throws Exception
     */
    public function findAll(): array
    {
        $passAttr = $this->readPasswordsFile();

        if (!empty($passAttr)) {
            $passwords = [];
            foreach ($passAttr as $name => $value) {
                $passwords[] = new Password(['name' => $name, 'value' => $value]);
            }
            return $passwords;

        }

        return [];
    }


    /**
     * @throws Exception
     */
    public
    function create(array $attributes): object
    {
        if (!$this->isPasswordExist($attributes['name'])) {
            $password = new Password($attributes);
            $this->addPassword($attributes['name'], $attributes['value']);
        } else {
            throw new Exception("Password already exists.");
        }
        return $password;
    }

    /**
     * @throws Exception
     */
    public
    function addPassword(string $passwordName, string $passwordValue): void
    {
        $passwords = $this->readPasswordsFile();
        $passwords[$passwordName] = $passwordValue;
        $this->filesystemEncryptor->put($this->storagePath, json_encode($passwords));
    }

    /**
     * @throws Exception
     */
    public
    function update(array $attributes): bool
    {
        $passwords = $this->readPasswordsFile();

        if ($this->isPasswordExist($attributes['name'])) {
            $passwords[$attributes['name']] = $attributes['value'];
            $this->filesystemEncryptor->put($this->storagePath, json_encode($passwords));

            return true;
        }

        return false;
    }

    /**
     * @throws Exception
     */
    public
    function delete(int|string $id): bool
    {
        if ($this->isPasswordExist($id)) {
            $passwords = $this->readPasswordsFile();
            unset($passwords[$id]);
            $this->filesystemEncryptor->put($this->storagePath, json_encode($passwords));

            return true;
        }

        return false;
    }

    /**
     * @throws Exception
     */
    private
    function readPasswordsFile(): array
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
    private
    function isPasswordExist(string $passwordName): bool
    {
        $passwords = $this->readPasswordsFile();
        if (!array_key_exists($passwordName, $passwords)) {
            return false;
        }

        return true;
    }

}
