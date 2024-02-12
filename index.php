<?php
global $passwordsFilePath;

use App\AskHelper;
use App\Encryptor;
use App\Filesystem;
use App\FilesystemEncryptor;
use App\InputOutput;
use App\PasswordManager;
use App\Store;


require_once __DIR__ . "/config.php";

spl_autoload_register(/**
 * @throws Exception
 */ callback: function ($className) {
    $className = str_replace("\\", "/", $className);
    $className = str_replace("App/", "", $className);

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
