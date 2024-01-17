<?php

class Auth
{
    private Store $store;

    private string $masterPassword;

    public function __construct(Store $store, string $masterPassword)
    {
        $this->store = $store;
        $this->masterPassword = $masterPassword;
    }

    public function login($password): void
    {

        if($password === $this->masterPassword) {
            session_start();

            $_SESSION['auth'] = true;
        }
    }

    public function logout(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            unset($_SESSION['auth']);
            session_destroy();
        }
    }
    public function isAuth(): bool
    {
        return $_SESSION['auth'] ?? false;
    }

}