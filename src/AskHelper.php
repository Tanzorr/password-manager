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
        return $this->askField('password name');
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
        $fieldVal = $this->io->expect("Enter $fieldName: ");

        return $fieldVal ?: $this->retryField($fieldName);
    }

    private function retryField(string $fieldName): string
    {
        $this->io->writeln("Field $fieldName is empty.");

        return $this->askField($fieldName);
    }
}