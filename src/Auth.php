<?php

class Auth
{
    private $store;

    private $masterPassword;

    public function __construct(Store $store)
    {
        $this->store = $store;
        $this->masterPassword = 'master';
    }

    public function login($password): void
    {
        $masterPasswordValue = $this->store->getPassword($this->masterPassword);

        if($password === $masterPasswordValue) {
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