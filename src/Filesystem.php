<?php

class Filesystem
{

    public function get($path): string
    {
        return file_get_contents($path);
    }

    public function put($path, $content): int
    {
        return file_put_contents($path, $content);
    }

    public function exists($path): bool
    {
        return file_exists($path);
    }
}