<?php

spl_autoload_register(function ($class) {
    $class = str_replace('\\', '/', $class);
    require_once __DIR__ . "/$class.php";
});

$storeHandler = new StoreHandler(Store::class, PasswordEncryptor::class);
$passwordManager = new PasswordManager($storeHandler);
$passwordManager->run();
