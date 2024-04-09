<?php

namespace App;

interface FilesystemInterface
{
    public function  exists(string $path): bool;
    public function  get(string $path): ?string;
    public function  put(string $path, string $content): int;
}
