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
        $decoded = base64_decode($encryptedPassword);

        if ($decoded === false) {
            // Handle base64 decoding error
            return false;
        }

        list($encryptedPassword, $iv) = explode('::', $decoded, 2);

        if ($iv === false || $encryptedPassword === false) {
            // Handle incorrect format error
            return false;
        }

        // Pad the IV with zeros to make it 16 bytes long
        $paddedIV = str_pad($iv, 16, "\0");

        $password = openssl_decrypt($encryptedPassword, 'aes-256-cbc', $this->ecryptionKey, 0, $paddedIV);

        if ($password === false) {
            // Handle decryption failure
            return false;
        }

        return $password;
    }
}