<?php
require_once 'StoreHandler.php';
require_once 'PasswordEncryptor.php';
require_once 'Store.php';
require_once 'PasswordManager.php';


$storeHandler = new StoreHandler(new Store());
$passwordManager = new PasswordManager(StoreHandler::class, Store::class);
$passwordManager->run();
