<?php

spl_autoload_register(function ($className) {
    require_once __DIR__ . "/src/$className.php";
});

$storeHandler = new StoreHandler(Store::class, PasswordEncryptor::class);
$passwordManager = new PasswordManager($storeHandler);
$passwordManager->run();
