<?php

class Store
{
    private string $passwordsFilePath = '';
    private FilesystemInterface $filesystem;
    private InputOoutput $io;

    public function __construct(
        FilesystemInterface $filesystem = null,
        string              $passToFile = '',
        InputOoutput        $io = null
    )
    {
        $this->passwordsFilePath = $passToFile;
        $this->filesystem = $filesystem ?? new Filesystem();
        $this->io = $io ?? new InputOoutput();
    }

    public function addPassword(string $passwordName, string $passwordValue): void
    {
        $passwords = $this->readPasswordsFile();
        $passwords[$passwordName] = $passwordValue;
        $this->filesystem->put($this->passwordsFilePath, json_encode($passwords));
        $this->io->writeln("$passwordName Password added.");
    }

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

    public function showAllPasswords(): void
    {
        $passwords = $this->readPasswordsFile();
        if (is_array($passwords) && count($passwords) === 0) {
            $this->io->writeln("No passwords found.");
            return;
        }

        $this->io->writeln("=== Password list ===");
        foreach ($passwords as $key => $value) {
            $this->io->writeln("- " . $key);
        }
        $this->io->writeln("=== ^^^^^^^^^^^^^ ===");
    }

    private function readPasswordsFile(): string|array
    {
        if (!$this->filesystem->exists($this->passwordsFilePath)) {
            $this->filesystem->put($this->passwordsFilePath, json_encode([]));
        }

        $jsonContent = $this->filesystem->get($this->passwordsFilePath);

        if ($jsonContent === false || $jsonContent === '') {
            throw new LogicException('Access denied.');
        }

        return json_decode($jsonContent, true);
    }

    public function changePassword(string $passwordName, string $newPasswordValue): void
    {
        $passwords = $this->readPasswordsFile();
        if ($this->isPasswordExist($passwordName)) {
            $passwords[$passwordName] = $newPasswordValue;
            $this->filesystem->put($this->passwordsFilePath, json_encode($passwords));
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
