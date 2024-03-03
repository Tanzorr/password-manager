<?php
namespace App;


global $io;

use ReflectionException;

require_once __DIR__ . "/vendor/autoload.php";


$container = new Container();

try {
    $io = $container->get(InputOutput::class);
} catch (ReflectionException $e) {
    $io->writeln($e->getMessage());
}

$encryptionKey = $io->expect("Enter encryption name: ");

if($encryptionKey === ''){
    $io->writeln("Encryption name is empty.");
    exit;
}

$container->setParameter('encryptionKey', $encryptionKey);
try {
    $container->load('./service.yaml');
} catch (\Exception $e) {
    $io->writeln($e->getMessage());
    exit;
}

try {
    $passwordManager = $container->build(PasswordManager::class);
    $passwordManager->run();
} catch (ReflectionException $e) {
    $io->writeln($e->getMessage());
}
