<?php

class Auth
{
    private string $masterPassword;
    private InputOoutput $io;

    public function __construct(string $masterPassword, InputOoutput $io = null)
    {
        $this->masterPassword = $masterPassword;
        $this->io = $io ?? new InputOoutput();
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
