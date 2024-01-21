<?php

interface FilesystemInterface{
    public function exists(string $path): bool;

    public function put(string $path, string $content): int;

    public function get(string $path): string;
    public function mkdir(string $path): bool;

}
