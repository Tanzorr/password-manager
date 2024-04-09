<?php

use App\Config;
use App\InputOutput;
use App\PasswordManager;
use Illuminate\Container\Container;

require_once __DIR__ . "/vendor/autoload.php";

$container = Container::getInstance();

$container->singleton(Config::class, function() {
    return new Config([
        'storagePath' => __DIR__.'/passwords.json'
    ]);
});

$io = $container->get(InputOutput::class);

$encryptionKey = $io->expect("Enter encryption name: ");

if($encryptionKey === ''){
    $io->writeln("Encryption name is empty.");
    exit;
}

$container->get(Config::class)->set('encryptionKey', $encryptionKey);


try {
    $passwordManager = $container->get(PasswordManager::class);
    $passwordManager->run();
} catch (ReflectionException $e) {
    $io->writeln($e->getMessage());
}
