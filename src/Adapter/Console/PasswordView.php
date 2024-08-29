<?php

namespace App\Adapter\Console;

use App\AskHelper;
use App\Domain\Model\Password;
use PhpSchool\CliMenu\Builder\CliMenuBuilder;

class PasswordView
{

    public function __construct(
        private PasswordController $passwordController,
        private AskHelper          $askHelper
    )
    {
    }

    public function addPassword(string $vaultName): void
    {
        $passwordName = $this->askHelper->askPasswordName();
        $passwordValue = $this->askHelper->askPasswordValue();

        $this->passwordController->addPassword($passwordName, $passwordValue);
       // $this->menuBuilder->flesh("Password $passwordName added to vault $vaultName");
        $this->askHelper->displayText("Password $passwordName added to vault $vaultName");
    }

    public function displayPassword(Password $password, string $vault): void
    {
       // $this->askHelper->displayText("Password:" . $password->name . "In Vault:" . $vault);

        $menuBuilder = (new CliMenuBuilder())->setTitle('Password Menu actions:');

        $menuBuilder->addStaticItem("Password:" . $password->name . " In Vault:" . $vault);
        $menuBuilder->addStaticItem("********");

        $menuBuilder->addLineBreak("========");
        $menuBuilder->addItem("Show password", fn() => $this->showPassword($password->name));
        $menuBuilder->addItem("Edit password", fn() => $this->changePassword($password->name));
        $menuBuilder->addItem("Delete password", fn() => $this->deletePassword($password->name));

        $menuBuilder->build()->open();
    }

    private function showPassword($passwordName): void
    {
        $this->askHelper->displayText("Password: $passwordName");
        $this->askHelper->displayText($this->passwordController->showPassword($passwordName));
    }

    private function changePassword(string $passwordName): void
    {
        $this->passwordController->changePassword($passwordName, $this->askHelper->askPasswordValue());
        $this->askHelper->displayText("$passwordName Password updated.");
    }

    private function deletePassword(string $passwordName): void
    {
        if (!$this->passwordController->deletePassword($passwordName)) {
            $this->askHelper->displayText("Password $passwordName not found");
            return;
        }
        $this->askHelper->displayText("$passwordName Password deleted.");
    }

}