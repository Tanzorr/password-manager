<?php

namespace App\Repository;

use App\FilesystemEncryptor;
use App\FilesystemInterface;
use App\Model\Password;
use Exception;

class PasswordRepository implements RepositoryInterface
{
    public function __construct(
        private FilesystemInterface $filesystemEncryptor,
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
        $this->filesystemEncryptor->put($this->storagePath, json_encode(
            array_merge($this->readPasswordsFile(), [$passwordName => $passwordValue])
        ));
    }

    /**
     * @throws Exception
     */
    public
    function update(array $attributes): bool
    {
        if ($this->isPasswordExist($attributes['name'])) {
            $this->filesystemEncryptor->put($this->storagePath, json_encode(
                $this->readPasswordsFile(),
                [$attributes['name'] => $attributes['value']]
            ));

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
    private function readPasswordsFile(): array
    {
        $passwords = $this->filesystemEncryptor->get($this->storagePath)
            ?: json_encode([]);

        if ($passwords === '') {
            throw new Exception('Access denied');
        }

        return json_decode($passwords, true);
    }


    /**
     * @throws Exception
     */
    private function isPasswordExist(string $passwordName): bool
    {
        return array_key_exists($passwordName, $this->readPasswordsFile());
    }
}