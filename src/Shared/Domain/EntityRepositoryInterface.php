<?php

namespace App\Shared\Domain;

interface EntityRepositoryInterface
{

    public function getByUuid(string $uuid);

    public function getUuidBySlug(string $slug);

    public function existByUuid(string $uuid): bool;

    public function existBySlug(string $slug): bool;

    public function getNotDeleted(): array;

    public function getNotDeletedSortedByName(): array;

    public function getNotDeletedByPage(int $offset = 0, int $limit = 10): ?array;

    public function getNotDeletedByPageSortedByName(int $offset = 0, int $limit = 10): ?array;

    public function findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null);

}
