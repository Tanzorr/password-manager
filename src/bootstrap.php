<?php

# этот фаил для консольных команд
use App\Core\Console\InputOutput;
use App\Domain\Handler\Query\GetVaultListQueryHandler;
use App\Domain\Query\GetVaultListQuery;
use Illuminate\Container\Container;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Config\Repository as ConfigRepositoryInterface;
use League\Tactician\CommandBus;

require_once __DIR__ . "/../vendor/autoload.php";

$container = Container::getInstance();

$container->singleton(ConfigRepositoryInterface::class, function () {
    $configValues = require_once __DIR__ . '/../config/app.php';
    return new Repository($configValues);
});

$io = $container->get(InputOutput::class);
$encryptionKey = '';

$container->get(ConfigRepositoryInterface::class)->set('encryptionKey', $encryptionKey);


$container->bind(CommandBus::class, function() use($container) {

    // todo write a resolver through an attribute
    $commandBus = \League\Tactician\Setup\QuickStart::create([
        GetVaultListQuery::class => $container->get(GetVaultListQueryHandler::class),
    ]);
    return $commandBus; 
});
