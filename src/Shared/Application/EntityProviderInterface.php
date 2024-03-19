<?php

namespace App\Shared\Application;

interface EntityProviderInterface
{
    public function getEntityByUuid(string $uuid);

    public function getNotDeletedEntitiesSortedByName(): array;

    public function getNotDeletedEntitiesByPage(int $offset, int $count): array;

    public function getEntitiesByField(string $field, object $value): array;

    public function getNotDeletedEntitiesByField(string $field, object $value): array;

    public function getEntitiesByFields(array $fields, object $value): array;

    public function getNotDeletedEntitiesByFields(array $fields, object $value): array;

}
