<?php

namespace App;

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

    private function askField(string $fieldName): string
    {
        $fieldVal = $this->io->expect("Enter $fieldName: ");

        if ($fieldVal === '') {
            echo "Field $fieldName is empty.\n";
            return $this->askField($fieldName);
        }
        return $fieldVal;
    }
}