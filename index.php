<?php
namespace App;


use App\InputOutput;
use App\PasswordManager;
use ReflectionException;
use App\FilesystemEncryptor;

require_once __DIR__ . "/config.php";
require_once __DIR__ . "/vendor/autoload.php";
global $passwordsFilePath;


$io = new InputOutput();

global $encryptorName;
$encryptorName = $io->expect("Enter your password: ");

if($encryptorName === ''){
   $io->writeln("Password is empty.");
    exit();
}

$container = new Container();

try {
    $passwordManager = $container->resolveClass(PasswordManager::class);
    $passwordManager->run();
} catch (ReflectionException $e) {
    echo $e->getMessage();
}
