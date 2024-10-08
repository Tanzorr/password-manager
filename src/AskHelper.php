<?php

namespace App;

use App\Core\Console\InputOutput;

class AskHelper
{
    public function __construct(private InputOutput $io)
    {
    }

    public function askPasswordName(): string
    {
        $passwordName = '';
        echo "Enter password name: ";

        while (true) {
            $char = fgetc(STDIN);

            if ($char === PHP_EOL) {
                break;
            }

            echo $char;
            $passwordName .= $char;
        }

        return trim($passwordName);
    }


    public function askPasswordValue(): string
    {
        return $this->askField('password value');
    }

    public function askVaultName(): string
    {
        return $this->askField('vault name');
    }

    private function askField(string $fieldName): string
    {
        $fieldVal = $this->io->askText("Enter $fieldName: ");

        return $fieldVal ?: $this->retryField($fieldName);
    }

    private function retryField(string $fieldName): string
    {
        $this->io->writeln("Field $fieldName is empty.");

        return $this->askField($fieldName);
    }

    public function displayText($input, bool $required = false): string
    {
        return $this->io->askText($input, $required);
    }
}