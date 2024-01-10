<?php

class IO
{
        public function writeln($text): void
        {
                echo $text . "\n";
        }

        public function expect($text): string
        {
                echo $text . "\n";
                return readline();
        }
}