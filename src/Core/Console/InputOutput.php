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
        $this->writeln($value);
        if(!$value){
            $this->clearScreen();
            $this->writeln("Value is required");
            $this->expect($text, $required);
        }

        return $value;
    }

    public function clearScreen(): void
    {
      exec('clear');
    }
}