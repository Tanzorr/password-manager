<?php

use App\Adapter\Console\VaultController;
use App\Core\Console\InputOutput;
use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Contracts\Config\Repository as ConfigRepositoryInterface;


require_once __DIR__ . "/../vendor/autoload.php";

$container = Container::getInstance();

$container->singleton(ConfigRepositoryInterface::class, function () {
    $configValues = require_once __DIR__ . '/../config/app.php';
    return new Repository($configValues);
});

$io = $container->get(InputOutput::class);
$encryptionKey = 'test';

$container->get(ConfigRepositoryInterface::class)->set('encryptionKey', $encryptionKey);

try {
    $passwordManager = $container->get(VaultController::class);
    $passwordManager->run();
} catch (ReflectionException $e) {
    $io->writeln($e->getMessage());
}
