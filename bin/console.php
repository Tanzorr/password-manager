<?php


use App\Adapter\Console\VaultView;
use App\Core\Console\InputOutput;
use Illuminate\Container\Container;

require_once __DIR__."/../src/bootstrap.php";

$container = Container::getInstance();

try {
    $passwordManager = $container->get(VaultView::class);
    $passwordManager->run();
}catch (ReflectionException $e) {
    $io = $container->get(InputOutput::class);
    $io->writeln("<error>{$e->getMessage()}</error>");
}