<?php

namespace App\Core\Console;

class InputOutput
{
    public function writeln(string $text): void
    {
        echo $text . "\n";
    }

    public function write(string $text): void
    {
        echo $text;
    }

    public function expect(string $text, bool $required = false): string
    {
        $this->write($text);
        $value = trim(readline());
        if(!$value) {
            $this->clearScreen();
            $this->io->writeln("Value cannot be empty.");
            $this->expect($text, $required);
        }

        return $value;
    }

    private function clearScreen(): void
    {
        exec('clear');
    }
}
