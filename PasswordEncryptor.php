<?php

define('ENCRYPTION_KEY', 'supersecretkey');

class PasswordEncryptor
{
    private $ecryptionKey;

    public function __construct($ecryptionKey)
    {
        $this->ecryptionKey = $ecryptionKey;
    }

    public function encryptPassword($password)
    {
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
        $encryptedPassword = openssl_encrypt($password, 'aes-256-cbc', $this->ecryptionKey, 0, $iv);
        return base64_encode($encryptedPassword . '::' . $iv);
    }

    public function decryptPassword($encryptedPassword)
    {
        list($encryptedPassword, $iv) = explode('::', base64_decode($encryptedPassword), 2);
        return openssl_decrypt($encryptedPassword, 'aes-256-cbc', $this->ecryptionKey, 0, $iv);
    }
}