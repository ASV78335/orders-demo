<?php

namespace App\AbstractContainer\Application\Command;

use App\AbstractContainer\Domain\Entity;
use App\Shared\Application\Command\CommandInterface;
use App\Shared\Application\Command\DTOCreateInterface;
use App\Shared\Application\Command\DTOUpdateInterface;
use App\Shared\Application\EntityProviderInterface;
use App\Shared\Application\Exception\RequestParsingException;
use App\Shared\Application\Query\DTOItemInterface;
use App\Shared\Domain\EntityRepositoryInterface;
use App\Shared\Domain\Exception\BusinessLogicException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\User\UserInterface;

abstract class AbstractCommandInteractor implements CommandInterface
{

    protected function __construct(
        protected $entityName,
        private $accessManager,
        private $linkChecker,
        private readonly EntityProviderInterface $entityProvider,
        private readonly EntityRepositoryInterface $repository,
        protected AccessDeniedException $accessDeniedException,
        protected ?BusinessLogicException $existsException,
        protected DTOCreateInterface $createCommand,
        protected DTOUpdateInterface $updateCommand
    )
    {
    }

    abstract protected function getAdditionalValues(UserInterface $user, ?Entity $entity, $request): array;

    public function create(UserInterface $user, DTOCreateInterface $request): ?DTOItemInterface
    {
        if (!$request instanceof $this->createCommand) throw new RequestParsingException();

        $additionalValues = $this->getAdditionalValues($user, null, $request);

        $entity = $this->entityName::fromCreateRequest($request, $additionalValues);
        if (!$this->accessManager->canCreate($user, $entity)) throw new $this->accessDeniedException();

        $this->repository->save($entity);

        return $entity->toResponseItem();
    }

    public function update(UserInterface $user, DTOUpdateInterface $request, string $uuid): ?DTOItemInterface
    {
        if (!$request instanceof $this->updateCommand) throw new RequestParsingException();
        $request->setUuid($uuid);

        $entity = $this->entityProvider->getEntityByUuid($uuid);

        if (!$this->accessManager->canEdit($user, $entity)) throw new $this->accessDeniedException();

        $additionalValues = $this->getAdditionalValues($user, $entity, $request);

        $updatedEntity = $this->entityName::fromUpdateRequest($entity, $request, $additionalValues);
        $this->repository->save($updatedEntity);

        return $updatedEntity->toResponseItem();
    }

    public function delete(UserInterface $user, string $uuid): void
    {
        $entity = $this->entityProvider->getEntityByUuid($uuid);

        if (!$this->accessManager->canEdit($user, $entity)) throw new $this->accessDeniedException;

        if ($this->linkChecker->check($entity)) $entity->makeDeleted();

        $this->repository->save($entity);
    }
}
