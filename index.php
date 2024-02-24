<?php
namespace App;


use ReflectionException;

require_once __DIR__ . "/config.php";
require_once __DIR__ . "/vendor/autoload.php";
global $storagePath;


$container = new Container();

$io = $container->get(InputOutput::class);

$encryptionKey = $io->expect("Enter encryption name: ");

if($encryptionKey === ''){
    $io->writeln("Encryption name is empty.");
    exit;
}

$container->setParameter('encryptionKey', $encryptionKey);
$container->setParameter('storagePath', './passwords.json');


try {
    $passwordManager = $container->build(PasswordManager::class);
    $passwordManager->run();
} catch (ReflectionException $e) {
    echo $e->getMessage();
}
