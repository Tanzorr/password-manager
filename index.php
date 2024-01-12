<?php

spl_autoload_register(function ($className) {
    require_once __DIR__ . "/src/$className.php";
});

$io = new IO();

$askHelper = new AskHelper($io);

$store =  new Store(
    new Filesystem(),
    new Encryptor(),
    "passwords.json",
    null,
    $io
);

$passwordManager = new PasswordManager($io, $store, $askHelper);
$passwordManager->run();
