<?php

namespace App\Shared\Application;

use App\Shared\Domain\EntityRepositoryInterface;

abstract class BaseEntityProvider implements EntityProviderInterface
{
    public function __construct(
        private readonly EntityRepositoryInterface $repository
    )
    {
    }

    abstract public function getEntityByUuid(string $uuid);

    public function getNotDeletedEntities(): array
    {
        return $this->repository->getNotDeleted();
    }

    public function getNotDeletedEntitiesSortedByName(): array
    {
        return $this->repository->getNotDeletedSortedByName();
    }

    public function getNotDeletedEntitiesByPage(int $offset, int $count): array
    {
        return $this->repository->getNotDeletedByPage($offset, $count);
    }

    public function getNotDeletedEntitiesByPageSortedByName(int $offset, int $count): array
    {
        return $this->repository->getNotDeletedByPageSortedByName($offset, $count);
    }

    public function getEntitiesByField(string $field, object $value): array
    {
        return $this->repository->findBy([$field => $value]);
    }

    public function getNotDeletedEntitiesByField(string $field, object $value): array
    {
        return $this->repository->findBy([$field => $value, 'deletedAt' => null]);
    }

    public function getEntitiesByFields(array $fields, object $value): array
    {
        $result = [];
        foreach ($fields as $field) {
            $result = array_merge($result, $this->repository->findBy([$field => $value]));
        }

        return $result;
    }

    public function getNotDeletedEntitiesByFields(array $fields, object $value): array
    {
        $result = [];
        foreach ($fields as $field) {
            $result = array_merge($result, $this->repository->findBy([$field => $value, 'deletedAt' => null]));
        }

        return $result;
    }
}
