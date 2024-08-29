<?php

namespace App\Adapter\Console;


use App\AskHelper;
use App\Core\Console\InputOutput;
use App\Domain\Model\Password;
use PhpSchool\CliMenu\Builder\CliMenuBuilder;

class PasswordView
{

    public function __construct(
        private PasswordController $passwordController,
        private InputOutput         $io,
        private AskHelper           $askHelper
    )
    {
    }

    /**
     * @throws \Exception
     */
    public function addPassword(string $vaultName): void
    {
        $passwordName = $this->askHelper->askPasswordName();
        $passwordValue = $this->askHelper->askPasswordValue();

        $this->passwordController->addPassword($passwordName, $passwordValue);
        $this->io->askText("Password $passwordName added to vault $vaultName");
    }

    public function displayPassword(Password $password, string $vault): void
    {
        $menuBuilder = (new CliMenuBuilder())->setTitle('Password Menu actions:');

        $menuBuilder->addStaticItem("Password:" . $password->name . " In Vault:" . $vault);
        $menuBuilder->addStaticItem("********");

        $menuBuilder->addLineBreak("========");
        $menuBuilder->addItem("Show password", fn() => $this->showPassword($password));
        $menuBuilder->addItem("Edit password", fn() => $this->changePassword($password->name));
        $menuBuilder->addItem("Delete password", fn() => $this->deletePassword($password->name));

        $menuBuilder->build()->open();
    }

    /**
     * @throws \Exception
     */
    private function showPassword(Password $password): void
    {
        $this->io->askText("Value: $password->value");
    }

    /**
     * @throws \Exception
     */
    private function changePassword(string $passwordName): void
    {
        $this->passwordController->changePassword($passwordName, $this->askHelper->askPasswordValue());
        $this->io->askText("$passwordName Password updated.");
    }

    /**
     * @throws \Exception
     */
    private function deletePassword(string $passwordName): void
    {
        if (!$this->passwordController->deletePassword($passwordName)) {
            $this->io->askText("Password $passwordName not found");
            return;
        }
        $this->io->askText("$passwordName Password deleted.");
    }

}