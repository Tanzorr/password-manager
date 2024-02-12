<?php

class FilesystemEncryptor implements FilesystemInterface
{
    public function __construct(
        protected Filesystem $filesystem,
        protected Encryptor  $encryptKay
    )
    {
    }

    public function exists(string $path): bool
    {
        return $this->filesystem->exists($path);
    }

    public function get(string $path): string
    {
        return $this->encryptKay->decrypt($this->filesystem->get($path));
    }

    public function put(string $path, string $content): int
    {
        return $this->filesystem->put($path, $this->encryptKay->encrypt($content));
    }
}