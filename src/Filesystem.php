<?php

namespace App;
class Filesystem
{
    public function get(string $storagePath): string
    {
        return file_get_contents($storagePath);
    }

    public function put(string $storagePath, string $content): int
    {
        return file_put_contents($storagePath, $content);
    }

    public function exists(string $storagePath): bool
    {
        return file_exists($storagePath);
    }
}