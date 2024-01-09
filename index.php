<?php
require_once 'StoreHandler.php';
require_once 'PasswordEncryptor.php';
require_once 'Store.php';
require_once 'PasswordManager.php';


$storeHandler = new StoreHandler(Store::class, PasswordEncryptor::class);
$passwordManager = new PasswordManager($storeHandler);
$passwordManager->run();
