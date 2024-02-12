<?php
global $passwordsFilePath;

require_once __DIR__ . "/config.php";

spl_autoload_register(function ($className) {
    require_once __DIR__ . "/src/$className.php";
});

$inputOutput = new InputOutput();

$askHelper = new AskHelper($inputOutput);

$store = new Store(
    new FilesystemEncryptor(
        new Filesystem(),
        new Encryptor($inputOutput->expect("Master password >>: ")
        )),
        $passwordsFilePath,
        $inputOutput
    );

$passwordManager = new PasswordManager($inputOutput, $store, $askHelper);
$passwordManager->run();
