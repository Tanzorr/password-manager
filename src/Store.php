<?php

@require_once 'helper.php';

class Store
{
    private $passwordsFile = 'passwords.json';
    private $masterPass;

    private $filesystem;
    private $encryptor;
    private $passToFile;

    private $io;

    public function __construct(
        Filesystem $filesystem = null,
        Encryptor  $encryptor = null,
        string     $passToFile = 'passwords.json',
                   $masterPass = null,
        IO         $io = null
    )
    {
        $this->passwordsFile = $passToFile;
        $this->filesystem = $filesystem ?? new Filesystem();
        $this->encryptor = $encryptor ?? new Encryptor();
        $this->passToFile = $passToFile;
        $this->masterPass = $masterPass;
        $this->io = $io ?? new IO();
    }

    public function addPassword($passwordName, $passwordValue): void
    {
        $passwords = $this->readPasswordsFile();
        $passwords[$passwordName] = $passwordValue;
        $this->writePasswordsFile($passwords);
        $this->io->writeln("$passwordName Password added.");
    }

    public function getPassword($passwordName)
    {
        $passwords = $this->readPasswordsFile();
        if(!array_key_exists($passwordName, $passwords)){
            $this->io->writeln("Password not found.");
            return;
        }
        $this->io->writeln($this->encryptor->decrypt($passwords[$passwordName]));
    }

    public function deletePassword($passwordName): void
    {
        $passwords = $this->readPasswordsFile();
        if(!array_key_exists($passwordName, $passwords)){
            $this->io->writeln("Password not found.");
            return;
        }
        unset($passwords[$passwordName]);
        $this->writePasswordsFile($passwords);
    }

    public function showAllPasswords(): void
    {
        $passwords = $this->readPasswordsFile();

        if (is_array($passwords) && count($passwords) === 0) {
            echo "No passwords found.\n";
            return;
        }

        foreach ($passwords as $key => $value) {
            echo "Password name: " . $key . "\n";
        }
    }

    private function readPasswordsFile(): string|array
    {
        if (!$this->filesystem->exists($this->passwordsFile)) {
            $this->io->writeln("No passwords found.");
        }

        $passwords = $this->filesystem->get($this->passwordsFile);

        if ($passwords === false) {
            return [];
        }

        return json_decode($passwords, true);
    }


    private function writePasswordsFile($passwords): void
    {
        $passwords = json_encode($passwords);
        file_put_contents($this->passwordsFile, $passwords);
    }

    public function changePassword($passwordName, $newPasswordValue): void
    {
        $passwords = $this->readPasswordsFile();
        $encryptNewPasswordValue = $this->encryptor->encrypt($newPasswordValue);
        if(!array_key_exists($passwordName, $passwords)){
            $this->io->writeln("Password not found.");
            return;
        }

        $passwords[$passwordName] = $encryptNewPasswordValue;
        $this->writePasswordsFile($passwords);
        $this->io->writeln("Password $passwordName changed.");
    }
}