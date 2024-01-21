<?php

class InputOoutput
{
    public function writeln(string $text): void
    {
        echo $text . "\n";
    }

    public function expect(string $text): string
    {
        return trim(readline($text. ' >> '));
    }
}
