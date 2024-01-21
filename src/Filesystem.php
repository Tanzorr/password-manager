<?php

class Filesystem implements FilesystemInterface
{

    public function get(string $path): string
    {
        return file_get_contents($path);
    }

    public function put(string $path, string $content): int
    {
        return file_put_contents($path, $content);
    }

    public function exists(string $path): bool
    {
        return file_exists($path);
    }

    public function mkdir(string $path): bool
    {
        return mkdir($path);
    }
}
