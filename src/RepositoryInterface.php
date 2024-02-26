<?php

namespace App;


interface RepositoryInterface
{
    public function create(array $attributes): object;

    public function update(int|string $id): bool;

    public function delete(int|string $id): bool;

    public function find(int|string $id): ?object;

    public function all(): array;
}
