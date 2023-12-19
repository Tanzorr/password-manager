<?php

class PasswordManager
{
    private $passwordFile = 'passwords.json';

    public function run()
    {
        echo "Welcome to Password Manager!\n";

        $this->showMenu();

        $this->getChosenPassword();
    }

    private function showMenu()
    {
        echo "\nMenu actions: \n";
        echo "1. Show password\n";
        echo "2. Add password\n";
        echo "3. Delete password\n";
        echo "4. Change password\n";
        echo "5. Show all passwords names\n";
        echo "q. Exit\n";
    }

    private function getChosenPassword()
    {
        $selectedChoseFromMenu = readline("Enter your choice action: ");
        $this->processChoice($selectedChoseFromMenu);
    }

    private function processChoice($choice)
    {
        switch ($choice) {
            case '1':
                $this->getPassword();
                break;
            case '2':
                $this->setPassword();
                break;
            case '3':
                $this->deletePassword();
                break;
            case '4':
                $this->replacePassword();
                break;
            case '5':
                $this->showAllPasswords();
                break;
            case 'q':
                // do nothing, the loop will exit
                break;
            default:
                echo "Некорректный выбор. Попробуйте снова.\n";
        }
    }

    private function showAllPasswords()
    {
        $arrayPasswords = $this->getPasswords();
        foreach ($arrayPasswords as $key => $value) {
            echo "Password name: " . $key. "\n";
        }
    }

    private function setPassword()
    {
        $data = $this->getProcessing();
        $arrayPasswords = $data['arrayPassword'];
        $passwordValue= readline("Enter password value");
        $passwordEncode = base64_encode($passwordValue);

        if(isset($arrayPasswords[$data['passwordName']])) {
            echo "Password with this name already exists";
            return;
        }
        $arrayPasswords[$data['passwordName']] = $passwordEncode;
        file_put_contents($this->passwordFile, json_encode($arrayPasswords));
    }

    private function getPassword()
    {
        $data = $this->getProcessing();

        $arrayPasswords = $data['arrayPassword'];
        if(!isset($arrayPasswords[$data['passwordName']])) {
            echo "Password with this name hasn't found";
            return;
        }

        $passwordEncode = $arrayPasswords[$data['passwordName']];
        $passwordDecode = base64_decode($passwordEncode);

        var_dump('password_verify', $passwordDecode, $passwordEncode);

        echo "Password: " . $passwordDecode;
    }


    private function getProcessing(): array
    {
        $arrayPasswords = $this->getPasswords();
        $passwordName= readline("Enter password name");

        return ['arrayPassword'=>$arrayPasswords, 'passwordName'=>$passwordName];
    }

    private function getPasswords(): array
    {
        $stringPasswords = $this->passwordFile ? file_get_contents($this->passwordFile) : [];
        return $stringPasswords ? json_decode($stringPasswords, true) : [];
    }

    private function deletePassword()
    {
        $data = $this->getProcessing();
        unset($data['arrayPassword'][$data['passwordName']]);
        file_put_contents($this->passwordFile, json_encode($data['arrayPassword']));
    }

    private function replacePassword()
    {
        $data = $this->getProcessing();
        $passwordValue= readline("Enter new password value");
        $data['arrayPassword'][$data['passwordName']] =  $passwordValue;
        file_put_contents($this->passwordFile, json_encode($data['arrayPassword']));
    }
}

$passwordManager = new PasswordManager();
$passwordManager->run();
