<?php

namespace App\Shared\Application;

interface EntityProviderExtendedInterface
{

    public function getEntityByUuid(string $entityName, string $uuid);

    public function getNotDeletedEntitiesSortedByName(string $entityName): array;

    public function getNotDeletedEntitiesByPage(string $entityName, int $offset, int $count): array;

    public function getEntitiesByField(string $entityName, string $field, object $value): array;

    public function getNotDeletedEntitiesByField(string $entityName, string $field, object $value): array;

    public function getEntitiesByFields(string $entityName, array $fields, object $value): array;

    public function getNotDeletedEntitiesByFields(string $entityName, array $fields, object $value): array;

}
