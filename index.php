<?php
global $userPassword, $passwordsFilePath, $encryptKay;

require_once __DIR__ . "/config.php";

spl_autoload_register(function ($className) {
    require_once __DIR__ . "/src/$className.php";
});

$inputOutput = new InputOoutput();

$askHelper = new AskHelper($inputOutput);

$store =  new Store(
    new Filesystem(),
    new Encryptor($encryptKay),
    $passwordsFilePath,
    $inputOutput
);

$auth = new Auth($userPassword, $inputOutput);

$auth->login($inputOutput->expect("Master password: "));

if(!$auth->isAuth()){
    $inputOutput->writeln("Please login.");
    exit;
}

$passwordManager = new PasswordManager($inputOutput, $store, $askHelper, $auth);
$passwordManager->run();
