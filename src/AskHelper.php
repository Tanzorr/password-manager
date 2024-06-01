<?php

namespace App;

class AskHelper
{
    public function __construct(private InputOutput $io)
    {
    }

    # методы формата askSomethingName принадлежат конкретному контексту, их лучше иметь в том месте
    # где ты пишешь связанный с ними код, тем более что сами методы между собой отличаются минимально
    private function prompt(string $fieldName): string
    {
        $fieldVal = $this->io->expect($fieldName);

        return $fieldVal ?: $this->retryField($fieldName);
    }

    private function retryField(string $fieldName): string
    {
        $this->io->writeln("Field $fieldName is empty.");

        return $this->prompt($fieldName);
    }
}
