<?php

class FilesystemEncryptor implements FilesystemInterface
{
    public function __construct(
        protected Filesystem $filesystem,
        protected Encryptor  $encryptor
    )
    {
    }

    public function exists(string $path): bool
    {
        return $this->filesystem->exists($path);
    }

    public function put(string $path, string $content): int
    {
        return $this->filesystem->put($path, $this->encryptor->encrypt($content));
    }

    public function get(string $path): string
    {
        return $this->encryptor->decrypt($this->filesystem->get($path));
    }

    public function mkdir(string $path): bool
    {
        return $this->filesystem->mkdir($path);
    }
}
