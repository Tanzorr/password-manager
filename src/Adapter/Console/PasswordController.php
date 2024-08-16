<?php

namespace App\Adapter\Console;

use App\AskHelper;
use App\Core\Console\InputOutput;
use App\Domain\Model\Password;
use App\Domain\Model\Vault;
use Exception;
use Illuminate\Contracts\Config\Repository;
use PhpSchool\CliMenu\Builder\CliMenuBuilder;

class PasswordController
{
    public function __construct(
        private InputOutput $io,
        private AskHelper   $askHelper,
        private Repository  $config
    )
    {
    }

    /**
     * @throws Exception
     */
    public function addPassword(VaultController $vaultController, string $vault): void
    {

        Password::create([
            'name' => $this->askHelper->askPasswordName(),
            'value' => $this->askHelper->askPasswordValue()
        ]);

        Vault::update([
            'name' => $this->config->get('activeVault'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        $vaultController->selectVaultItem($vault);
    }

    /**
     * @throws Exception
     */
    private function showPassword($passwordName): void
    {
        $this->io->writeln(Password::find($passwordName)->value);
    }

    public function displayPassword(Password $password, string $vault, VaultController $vaultController)
    {
        $this->io->writeln("Password:" . $password->name . "In Vault:" . $vault);

        $menuBuilder = (new CliMenuBuilder())->setTitle('Password Menu actions:');

        $menuBuilder->addItem("Password:" . $password->name . " In Vault:" . $vault, function () {
        });
        $menuBuilder->addItem("********", function () {
        });

        $menuBuilder->addItem("========", function () {
        });
        $menuBuilder->addItem("Show password", fn() => $this->showPassword($password->name));
        $menuBuilder->addItem("Edit password", fn() => $this->changePassword($password->name));
        $menuBuilder->addItem("Delete password", fn() => $this->deletePassword($password->name, $vault, $vaultController));
        $menuBuilder->addItem("back", fn() => $vaultController->selectVaultItem($vault));

        $menuBuilder->build()->open();
    }

    /**
     * @throws Exception
     */
    private function deletePassword(String $passwordName, string $vaultName, VaultController $vaultController): void
    {
        if (Password::delete($passwordName)) {
            $this->io->writeln("$passwordName Password deleted.");
            Vault::update([
                'name' => $this->config->get('activeVault'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }

        $vaultController->selectVaultItem($vaultName);
    }

    /**
     * @throws Exception
     */
    private function changePassword(String $passwordName): void
    {
        Password::update([
            'name' => $passwordName,
            'value' => $this->askHelper->askPasswordValue()
        ]);

        Vault::update([
            'name' => $this->config->get('activeVault'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }

    public function getAllPasswords(): array
    {
        return Password::findAll();
    }
}
