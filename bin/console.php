<?php
# этот фаил для консольных команд
use App\Adapter\Console\Controller\VaultController;
use App\Core\Console\InputOutput;
use Illuminate\Container\Container;

require_once __DIR__ . "/../src/bootstrap.php";

$container = Container::getInstance();

try {
    $passwordManager = $container->get(VaultController::class);
    $passwordManager->run();
} catch (ReflectionException $e) {
    $io = $container->get(InputOutput::class);
    $io->writeln($e->getMessage());
}
