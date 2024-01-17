<?php

class Store
{
    private $passwordsFile = '';
    private $masterPass;

    private $filesystem;
    private $encryptor;
    private $passToFile;

    private $io;

    public function __construct(
        Filesystem   $filesystem = null,
        Encryptor    $encryptor = null,
        string       $passToFile = null,
        string       $masterPass = null,
        InputOoutput $io = null
    )
    {
        $this->passwordsFile = $passToFile;
        $this->filesystem = $filesystem ?? new Filesystem();
        $this->encryptor = $encryptor ?? new Encryptor();
        $this->passToFile = $passToFile;
        $this->masterPass = $masterPass;
        $this->io = $io ?? new InputOoutput();
    }

    public function addPassword(string $passwordName, string $passwordValue): void
    {
        $passwords = $this->readPasswordsFile();
        $passwords[$passwordName] = $this->encryptor->encrypt($passwordValue);
        $this->filesystem->put($this->passwordsFile, json_encode($passwords));
        $this->io->writeln("$passwordName Password added.");
    }

    public function getPassword(string $passwordName): string
    {
        $passwords = $this->readPasswordsFile();
        if (!array_key_exists($passwordName, $passwords)) {
            return "Password not found.";
        }

        $decryptPassword = $this->encryptor->decrypt($passwords[$passwordName]);

        if ($decryptPassword) {
            return $decryptPassword;
        } else {
            return 'Password not found.';
        }
    }

    public function deletePassword(string $passwordName): void
    {
        $passwords = $this->readPasswordsFile();
        if (!array_key_exists($passwordName, $passwords)) {
            $this->io->writeln("Password not found.");
            return;
        }

        unset($passwords[$passwordName]);
        $this->filesystem->put($this->passwordsFile, json_encode($passwords));
    }

    public function showAllPasswords(): void
    {
        $passwords = $this->readPasswordsFile();
        if (is_array($passwords) && count($passwords) === 0) {
            $this->io->writeln("No passwords found.");
            return;
        }

        foreach ($passwords as $key => $value) {
            $this->io->writeln("Password name: " . $key);
        }
    }

    private function readPasswordsFile(): string|array
    {
        if (!$this->filesystem->exists($this->passwordsFile)) {
            $this->io->writeln("No passwords found.");
        }

        $passwords = $this->filesystem->get($this->passwordsFile);

        if ($passwords === false || $passwords === '') {
            return [];
        }

        return json_decode($passwords, true);
    }

    public function changePassword(string $passwordName, string $newPasswordValue): void
    {
        $passwords = $this->readPasswordsFile();
        $encryptNewPasswordValue = $this->encryptor->encrypt($newPasswordValue);

        if ($this->isPasswordExist($passwordName)) {
            $passwords[$passwordName] = $encryptNewPasswordValue;
            $this->filesystem->put($this->passwordsFile, json_encode($passwords));
            $this->io->writeln("Password $passwordName changed.");
        }
    }

    private function isPasswordExist(string $passwordName): bool
    {
        $passwords = $this->readPasswordsFile();
        if (!array_key_exists($passwordName, $passwords)) {
            $this->io->writeln("Password not found.");
            return false;
        }
        return true;
    }
}