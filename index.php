<?php
use App\InputOutput;
use App\PasswordManager;
use Illuminate\Container\Container;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Config\Repository as ConfigRepositoryInterface;


require_once __DIR__ . "/vendor/autoload.php";

$container = Container::getInstance();

$container->singleton(ConfigRepositoryInterface::class, function () {
    $configValues = require_once __DIR__ . '/config/app.php';
    return new Repository($configValues);
});

$io = $container->get(InputOutput::class);


$encryptionKey = $io->expect("Enter encryption name: ");

if($encryptionKey === ''){
    $io->writeln("Encryption name is empty.");
    exit;
}

$container->get(ConfigRepositoryInterface::class)->set('encryptionKey', $encryptionKey);



try {
    $passwordManager = $container->get(PasswordManager::class);
    $passwordManager->run();
} catch (ReflectionException $e) {
    $io->writeln($e->getMessage());
}
