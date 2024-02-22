<?php

namespace App;
class InputOutput
{
    public function writeln(string $text): void
    {
        echo $text . "\n";
    }

    public function expect(string $text): string
    {
        echo $text . "\n";
        return trim(readline());
    }
}