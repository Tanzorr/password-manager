<?php

// - Когда создаёшь класс PasswordRepository ты должен внутрь передававать новые обьекты а не названия класс
$store = new StoreHandler(
    new Store(),
    // мастер пароль должен один раз быть считан когда человек запускает приложение
    // и потом использоваться для расшифровки всего файла, а не каждого пароля по одиночке
    new Encryptor("master-password")
);
/*ВАЖНЫЙ МОМЕНТ
во время ревью я понял что у тебя StoreHandler и PasswordManager выполняют очень похожие задачи, но ты их разделил
проблема этих классов как и остальныйх классов в это проекте состоит в семантическом назначении каждого класса
у тебя должно быть следующее
PasswordManager - отвечает за бизнес логику (он оркестрирует классы, выводит список команд и обрабатывает их) (это много для одного класса, но чуть позже я расскажу как это красиво разобрать)
    использует PasswordRepository чтобы получить список паролей и модифирует их с помощью PasswordRepository
PasswordRepository - использует Filesystem чтобы получить фаил и расшифровывает его с помощью Encryptor
    когда человек выходит операция с файлом закончена вызывается PasswordRepository::persist который с помощью Encryptor зашифрует фаил и с помощью Filesystem запишет зашифрованное содержимое в фаил
Filesystem:
 - get -> file_get_contents
 - put -> file_put_contents
 - exists -> file_exists
Encryptor
 - encrypt
 - decrypt
InputOoutput:
 - writeln -> echo
 - expect -> readline

Твои классы страдают от черезмерных обязаностей (Single responsibility из SOLID)
чтобы этого избежать мы должны правильно обьявлять семантические связи между классами ещё до того как мы их будем использовать

Задачи
- все классы перенести в папку src
- использовать spl_autoload_register для того чтобы подгружать любые классы из папки src
- Когда создаёшь класс PasswordRepository ты должен внутрь передававать новые обьекты а не названия класс
Рефакторинг:
 - вытащить все упоминания echo в класс InputOoutput в которому буду функции expect(Ожидание ввода данных) и writeln (Вывод текста)
 - Операции с файловой системой вытаскивай в класс Filesystem (file_get_contents, file_put_contents, file_exists)
Переименуй классы:
 - Encryptor -> Encryptor, функции encrypt и decrypt. Это класс общего назначения и он не должен знать о существовании паролей вообще, он может использоваться в других целях
*/

// результирующай код будет выглядеть примерно так
$io = new InputOutput();
$masterPass = $io->expect("Master password");

$store = new Store(
    new Filesystem(),
    new Encryptor(),
    "./path-to-the-file",
    $masterPass
);

$application = new PasswordManager($io, $store);

$application->run();

// Есть ещэ много нюансов, но для этого нужен целый час разговоров и pair programming
