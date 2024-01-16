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

$auth = new Auth($store);

$auth->login($io->expect("Master password: "));

if(!$auth->isAuth()){
    $io->writeln("Wrong password.");
    exit;
}

$passwordManager = new PasswordManager($io, $store, $askHelper, $auth);
$passwordManager->run();
