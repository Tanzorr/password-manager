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
        if(!file_exists(dirname($storagePath))) {
            mkdir(dirname($storagePath), 0777, true);
        }

        return file_put_contents($storagePath, $content);
    }

    public function exists(string $storagePath): bool
    {
        return file_exists($storagePath);
    }

    public function delete(string $storagePath): bool
    {
        return unlink($storagePath);
    }
    public function getAllFiles(string $storagePath): array
    {
        if(scandir($storagePath) === false){
            return [];
        }

        return scandir($storagePath);
    }

    public function createFile(string $storagePath): bool
    {
        mkdir($storagePath,0777, true);
        return true;
    }
}