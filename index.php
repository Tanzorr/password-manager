<?php
global $passwordsFilePath;
use App\Container;
use App\InputOutput;
use App\PasswordManager;

require_once __DIR__ . "/config.php";

spl_autoload_register(/**
 * @throws Exception
 */ callback: function ($className) {
    $className = str_replace("\\", "/", $className);
    $className = str_replace("App/", "", $className);

    require_once __DIR__ . "/src/$className.php";
});

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
