<?php

const ENCRYPTION_KEY = 'superstructure';

class Encryptor
{

    private $encryptionKey = '';
    public function __construct($encryptionKey = ENCRYPTION_KEY)
    {
        $this->encryptionKey = $encryptionKey;
    }

    public function encrypt($string): string
    {
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
        $encryptedString = openssl_encrypt($string, 'aes-256-cbc', $this->encryptionKey, 0, $iv);
        return base64_encode($encryptedString . '::' . $iv);
    }

    public function decrypt($encryptedString): string|bool
    {
        $decoded = base64_decode($encryptedString);

        if ($decoded === false) {
            return false;
        }

        list($encryptedString, $iv) = explode('::', $decoded, 2);

        if ($iv === false || $encryptedString === false) {
            return false;
        }


        $paddedIV = str_pad($iv, 16, "\0");

        $result = openssl_decrypt($encryptedString, 'aes-256-cbc', $this->encryptionKey, 0, $paddedIV);

        if ($result === false) {
            return false;
        }

        return $result;
    }
}