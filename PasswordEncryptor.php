<?php

const ENCRYPTION_KEY = 'superstructure';

class PasswordEncryptor
{
    private  string $encryptionKey;

    public function __construct($encryptionKey)
    {
        $this->encryptionKey = $encryptionKey;
    }

    public function encryptPassword($password): string
    {
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
        $encryptedPassword = openssl_encrypt($password, 'aes-256-cbc', $this->encryptionKey, 0, $iv);
        return base64_encode($encryptedPassword . '::' . $iv);
    }

    public function decryptPassword($encryptedPassword): string|bool
    {
        $decoded = base64_decode($encryptedPassword);

        if ($decoded === false) {
            return false;
        }

        list($encryptedPassword, $iv) = explode('::', $decoded, 2);

        if ($iv === false || $encryptedPassword === false) {
            return false;
        }


        $paddedIV = str_pad($iv, 16, "\0");

        $password = openssl_decrypt($encryptedPassword, 'aes-256-cbc', $this->encryptionKey, 0, $paddedIV);

        if ($password === false) {
            return false;
        }

        return $password;
    }
}