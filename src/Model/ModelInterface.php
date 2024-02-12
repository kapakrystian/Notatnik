<?php

declare(strict_types=1);

namespace App\Model;

interface ModelInterface
{
    public function list(
        string $sortBy,
        string $sortOrder,
        int $pageNumber,
        int $pageSize
    ): array;

    public function search(
        string $sortBy,
        string $sortOrder,
        int $pageNumber,
        int $pageSize,
        ?string $phrase,
        ?string $date
    ): array;

    public function count(): int;

    public function searchCount(string $phrase): int;

    public function get(int $id): array;

    public function create(array $data): void;

    public function edit(int $id, array $data): void;

    public function delete(int $id): void;
}
