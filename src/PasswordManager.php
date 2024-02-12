<?php

use JetBrains\PhpStorm\NoReturn;

class PasswordManager
{
    private InputOutput $io;
    private Store $store;

    private AskHelper $askHelper;

    public function __construct(InputOutput $io, Store $store, AskHelper $askHelper)
    {
        $this->io = $io;
        $this->store = $store;
        $this->askHelper = $askHelper;
    }

    public function run(): void
    {
        $this->io->writeln("Welcome to Password Manager");

        while (true) {
            $this->showMenu();
            $action = $this->io->expect("Choose action: ");
            passthru('clear');
            $this->getChosenAction($action);
        }
    }

    private function showMenu(): void
    {
        $this->io->writeln("===============");
        $this->io->writeln("Menu actions:");
        $this->io->writeln("[s] Show password");
        $this->io->writeln("[a] Add password");
        $this->io->writeln("[d] Delete password");
        $this->io->writeln("[c] Change password");
        $this->io->writeln("[l] List all passwords names");
        $this->io->writeln("[q] Exit");
        $this->io->writeln("===============");
    }

    /**
     * @throws Exception
     */
    private function getChosenAction(string $action): void
    {
        match ($action) {
            "l" => $this->showAllPasswords(),
            "a" => $this->addPassword(),
            "s" => $this->showPassword(),
            "d" => $this->deletePassword(),
            "c" => $this->changePassword(),
            "q" => $this->logout(),
            default => $this->io->writeln("[ERROR]Unknown action"),
        };
    }

    /**
     * @throws Exception
     */
    private function addPassword(): void
    {
        $passwordName = $this->askHelper->askPasswordName();
        $passwordValue = $this->askHelper->askPasswordValue();

        $this->store->addPassword($passwordName, $passwordValue);
    }

    /**
     * @throws Exception
     */
    private function showPassword(): void
    {
        $passwordName = $this->askHelper->askPasswordName();
        $passwordValue = $this->store->getPassword($passwordName);

        $this->io->writeln($passwordValue);
    }

    /**
     * @throws Exception
     */
    private function deletePassword(): void
    {
        $passwordName = $this->askHelper->askPasswordName();
        $this->store->deletePassword($passwordName);
    }

    /**
     * @throws Exception
     */
    private function changePassword(): void
    {
        $passwordName = $this->askHelper->askPasswordName();
        $passwordValue = $this->askHelper->askPasswordValue();

        $this->store->changePassword($passwordName, $passwordValue);
    }

    private function showAllPasswords(): void
    {
        try {
            $passwords = $this->store->getAllPasswords();
            $this->io->writeln("===============");
            foreach ($passwords as $key => $value) {
                $this->io->writeln("Password name: " . $key);
            }
            $this->io->writeln("===============");

        } catch (Exception $e) {
            $this->io->writeln($e->getMessage());
        }
    }

    #[NoReturn] private function logout(): void
    {
        exit();
    }
}
