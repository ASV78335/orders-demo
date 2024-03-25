<?php

namespace App\AbstractContainer\Application\Query;

use App\AbstractContainer\Domain\Entity;
use App\Shared\Application\EntityProviderInterface;
use App\Shared\Application\Query\DTODetailsInterface;
use App\Shared\Application\Query\DTOItemInterface;
use App\Shared\Application\Query\DTOListInterface;
use App\Shared\Application\Query\QueryInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\User\UserInterface;

class AbstractQueryInteractor implements QueryInterface
{
    public const ENTITIES_PER_PAGE = 10;

    public function __construct(
        private $accessManager,
        private readonly EntityProviderInterface $entityProvider,
        protected AccessDeniedException $accessDeniedException,
        protected DTOListInterface $DTOList
    )
    {
    }

    public function getAll(UserInterface $user): DTOListInterface
    {
        $entities = $this->entityProvider->getNotDeletedEntities();
        if (empty($entities)) return $this->DTOList;

        foreach ($entities as $entity) {
            if (!$this->accessManager->canView($user, $entity)) throw new $this->accessDeniedException();
        }

        $items = array_map(fn (Entity $entity) => $entity->toResponseItem(), $entities);
        $this->DTOList->setItems($items);

        return $this->DTOList;
    }

    public function getSelection(UserInterface $user, int $offset, ?int $count): DTOListInterface
    {
        $count = $count ?? self::ENTITIES_PER_PAGE;

        $entities = $this->entityProvider->getNotDeletedEntitiesByPage($offset, $count);
        if (empty($entities)) return $this->DTOList;

        foreach ($entities as $entity) {
            if (!$this->accessManager->canView($user, $entity)) throw new $this->accessDeniedException();
        }

        $items = array_map(fn (Entity $entity) => $entity->toResponseItem(), $entities);
        $this->DTOList->setItems($items);

        return $this->DTOList;
    }

    public function getItem($user, $uuid): DTOItemInterface
    {
        $entity = $this->entityProvider->getEntityByUuid($uuid);

        if (!$this->accessManager->canView($user, $entity)) throw new $this->accessDeniedException();

        return $entity->toResponseItem();
    }

    public function getDetails($user, string $uuid): DTODetailsInterface
    {
        $entity = $this->entityProvider->getEntityByUuid($uuid);

        if (!$this->accessManager->canView($user, $entity)) throw new $this->accessDeniedException();

        return $entity->toResponseDetails();
    }
}
