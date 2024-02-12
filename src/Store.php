<?php

namespace App;
use Exception;

class Store
{
    public function __construct(
        private ?FilesystemInterface $filesystem = null,
        private readonly string      $passwordsFilePath = '',
        private ?InputOutput         $io = null
    )
    {
        $this->filesystem = $filesystem ?? new Filesystem();
        $this->io = $io ?? new InputOutput();
    }

    /**
     * @throws Exception
     */
    public function addPassword(string $passwordName, string $passwordValue): void
    {
        $passwords = $this->readPasswordsFile();
        $passwords[$passwordName] = $passwordValue;
        $this->filesystem->put($this->passwordsFilePath, json_encode($passwords));
        $this->io->writeln("$passwordName Password added.");
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
        $this->filesystem->put($this->passwordsFilePath, json_encode($passwords));
    }

    /**
     * @throws Exception
     */
    public function getAllPasswords():array
    {
        $passwords = $this->readPasswordsFile();
        if (is_array($passwords) && count($passwords) === 0) {
            throw new Exception('No passwords found');
        }

        return $passwords;
    }

    /**
     * @throws Exception
     */
    private function readPasswordsFile(): string|array
    {
        if (!$this->filesystem->exists($this->passwordsFilePath)) {
            $this->filesystem->put($this->passwordsFilePath, json_encode([]));
            throw new Exception("No passwords found.");
        }

        $passwords = $this->filesystem->get($this->passwordsFilePath);

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
            $this->filesystem->put($this->passwordsFilePath, json_encode($passwords));
            $this->io->writeln("Password $passwordName changed.");
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
}