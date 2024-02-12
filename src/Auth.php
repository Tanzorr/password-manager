<?php

class Auth
{
    private string $masterPassword;
    private InputOutput $io;

    public function __construct(string $masterPassword, InputOutput $io = null)
    {
        $this->masterPassword = $masterPassword;
        $this->io = $io ?? new InputOutput();
        session_start();
    }

    public function login(string $password): void
    {
        if ($password === $this->masterPassword) {
            $_SESSION['auth'] = true;
        } else {
            $this->io->writeln("Wrong password.");
        }
    }

    public function logout(): void
    {
        if (isset($_SESSION['auth'])) {
            unset($_SESSION['auth']);
            session_destroy();
        }
    }

    public function isAuth(): bool
    {
        return $_SESSION['auth'] ?? false;
    }
}
