<?php

namespace App;

use Exception;

class Store implements RepositoryInterface
{
    public function __construct(
        private readonly ?FilesystemEncryptor $filesystem = null,
        private string                        $storagePath = '',
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
        $this->filesystem->put($this->storagePath, json_encode($passwords));

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
        $passwords = $this->readPasswordsFile();

        if (!array_key_exists($passwordName, $passwords)) {
            $this->io->writeln("Password not found.");
            return;
        }

        unset($passwords[$passwordName]);
        $this->filesystem->put($this->storagePath, json_encode($passwords));
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
        if (!$this->filesystem->exists($this->storagePath)) {
            $this->filesystem->put($this->storagePath, json_encode([]));
            return [];
        }

        $passwords = $this->filesystem->get($this->storagePath);

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
            throw new Exception("Password not found.");
        }

        return true;
    }

    /**
     * @throws Exception
     */
    public function create(array $attributes): object
    {
        $passwords = $this->readPasswordsFile();

        if (!$this->isPasswordExist($attributes['name'])) {
            $passwords[$attributes['name']] = $attributes['value'];
            $this->filesystem->put($this->storagePath, json_encode($passwords));
        } else {
            throw new Exception("Password already exists.");
        }
        return $this;
    }

    public function update(int|string $id): bool
    {
        // TODO: Implement update() method.
    }

    public function delete(int|string $id): bool
    {
        // TODO: Implement delete() method.
    }

    public function find(int|string $id): ?object
    {
        // TODO: Implement find() method.
    }

    public function all(): array
    {
        // TODO: Implement all() method.
    }
}