<?php
use App\Core\Console\InputOutput;
use App\Domain\Handler\Query\GetVaultListQueryHandler;
use App\Domain\Query\GetVaultListQuery;
use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use League\Tactician\Setup\QuickStart;
use Psr\Container\ContainerInterface;
use Illuminate\Contracts\Config\Repository as ConfigRepositoryInterface;
use League\Tactician\CommandBus;

require_once __DIR__ . "/../vendor/autoload.php";

$container = Container::getInstance();

$container->singleton(ConfigRepositoryInterface::class, function () {
    $configValues = require __DIR__ . "/../config/app.php";
    return new Repository($configValues);
});

$io = $container->get(InputOutput::class);
$encryptionKey = '';

try {
    $container->get(ConfigRepositoryInterface::class)->set('encryptionKey', $encryptionKey);
} catch (\Psr\Container\NotFoundExceptionInterface|\Psr\Container\ContainerExceptionInterface $e) {
    new ErrorException($e->getMessage());
}

$container->bind(CommandBus::class, function() use($container) {

    // todo write a resolver through an attribute
    $commandBus = QuickStart::create([
        GetVaultListQuery::class => $container->get(GetVaultListQueryHandler::class),
    ]);
    return $commandBus;
});

