<?php
global $userPassword, $passwordsFile, $encryptKay;

require_once __DIR__ . "/config.php";

spl_autoload_register(function ($className) {
    require_once __DIR__ . "/src/$className.php";
});

$inputOutput = new InputOoutput();

$askHelper = new AskHelper($inputOutput);

$store =  new Store(
    new Filesystem(),
    new Encryptor($encryptKay),
    $passwordsFile,
    null,
    $inputOutput
);

$auth = new Auth($store, $userPassword);

$auth->login($inputOutput->expect("Master password: "));

if(!$auth->isAuth()){
    $inputOutput->writeln("Wrong password.");
    exit;
}

$passwordManager = new PasswordManager($inputOutput, $store, $askHelper, $auth);
$passwordManager->run();
