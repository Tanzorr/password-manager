<?php

global $passwordsFilePath;
use App\Container;
use App\InputOutput;
use App\PasswordManager;

require_once __DIR__ . "/config.php";

# autoloader можно вытащить в отдельный фаил который называется autoload.php
# но по сути уже с этого момента ты можешь официально пользоваться composer
# так что можешь удалить этот кусоче и заменить его на require __DIR__."/vendor/autoload.php" перед этим предварительно выполнив composer init
# с этого момента мы будем переходить к постепенному использованию пакетов
spl_autoload_register(/**
 * @throws Exception
 */ callback: function ($className) {
    $className = str_replace("\\", "/", $className);
    $className = str_replace("App/", "", $className);

    require_once __DIR__ . "/src/$className.php";
});


$container = new Container();

$io = $container->get(InputOutput::class);

$encryptionName = $io->expect("Enter your password");

if($encryptionName === '') {
    $io->writeln("Password is empty.");
    exit();
}

// таким образом мы решаем проблему глобальный переменный + мы получаем возможность конфигурировать контейнер
// в целом настройка контейнера во время рантайма не самая хорошая практика, но в данном случае можно закрыть на это глаза
$container->setParameter('encryptionKey', $encryptionName);
$container->setParameter('storagePath', __DIR__.'/passwords.json');

// Твои последующие задачи
// - [ ] composer init и настрой autoload из папки src если надо
// - [ ] создать services.yaml в котором будет секция parameters в которой ты обьявишь storagePath: "./password.json". Можешь использовать symfony/yaml для того чтобы читатть этот фаил.
//       Инициализируй контейнер через $container->load('./service.yaml') и внутри него загружай все параметры из этого файла внутрь контейнера для дальнейшего использования
// - [ ] обьяви интерфейс Repository с методами
//   create(array $attributes): object
//   delete(int|string $id): bool
//   update(int|string $id): bool
//   find(int|string $id): ?object
//   findAll(): array
// - [ ] Перепиши класс Store чтобы он имплементировал интерфейс репозитория
// - [ ] Обьяви класс Password(public array $attributes) и используй его вместо обычных массиво. Внутри $attributes у тебя будут поля 'name', 'value'
//
// все эти изменения позволят тебе сделать первый шаг к построению системы очень похожей на Laravel, мы будем использовать похожие инструменты, трюки и тд
// В дальнейшем мы очень аккуратно заменим использование файла на использование БД и перейдём на реальные пакеты, после чего в определённый моменты ты перепишешт это приложение на http формат используя пакеты из ларавеля// мы будем повышать планку через попытки имплементировать функционал существующи в лараеле у нас в текущем проекте. К примеру я тебе советую посмотреть в метод Container::build в ларавеле, он будет немного напомнить твоё решение
// репозиторий что мы имплементируем со временм будет заменён на Password::create/ Password::delete и тд через использование магических методов
// + мы добавим пару новых сущностей, к примеру storage как модель, чтобы можно было имплементровать более сложные паттерны, но это на потом


try {
    $passwordManager = $container->build(PasswordManager::class);
    $passwordManager->run();
} catch (ReflectionException $e) {
    echo $e->getMessage();
}
