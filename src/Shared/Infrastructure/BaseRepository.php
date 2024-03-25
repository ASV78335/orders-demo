<?php

namespace App\Shared\Infrastructure;

use App\Shared\Domain\EntityRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

abstract class BaseRepository implements EntityRepositoryInterface
{
    private EntityRepository $repository;
    public function __construct(private readonly EntityManagerInterface $em, $class)
    {
        $this->repository = $this->em->getRepository($class);
    }

    public function getByUuid(string $uuid): ?object
    {
        return $this->repository->findOneBy(['uuid.uuid' => $uuid]);
    }

    public function getUuidBySlug(string $slug)
    {
        $entity = $this->repository->findOneBy(['slug' => $slug]);

        return $entity->getUuid();
    }

    public function existByUuid(string $uuid): bool
    {
        return null !== $this->repository->findOneBy(['uuid.uuid' => $uuid]);
    }

    public function existBySlug(string $slug): bool
    {
        return null !== $this->repository->findOneBy(['slug' => $slug]);
    }

    public function getNotDeleted(): array
    {
        return $this->repository->createQueryBuilder('e')
            ->where('e.deletedAt IS NULL')
            ->getQuery()
            ->getResult();
    }

    public function getNotDeletedSortedByName(): array
    {
        return $this->repository->createQueryBuilder('e')
            ->where('e.deletedAt IS NULL')
            ->orderBy('e.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function getNotDeletedByPage(int $offset = 0, int $limit = 10): array
    {
        return $this->repository->createQueryBuilder('e')
            ->where('e.deletedAt IS NULL')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function getNotDeletedByPageSortedByName(int $offset = 0, int $limit = 10): array
    {
        return $this->repository->createQueryBuilder('e')
            ->where('e.deletedAt IS NULL')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->orderBy('e.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null): array
    {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    public function save($entity): void
    {
        $this->em->persist($entity);
        $this->em->flush();
    }
}
