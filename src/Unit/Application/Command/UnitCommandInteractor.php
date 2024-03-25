<?php

namespace App\Unit\Application\Command;

use App\AbstractContainer\Application\Command\AbstractCommandInteractor;
use App\AbstractContainer\Domain\Entity;
use App\Shared\Application\Command\CommandInterface;
use App\Unit\Application\UnitEntityProvider;
use App\Unit\Domain\Exception\UnitAccessDeniedException;
use App\Unit\Domain\Exception\UnitAlreadyExistsException;
use App\Unit\Domain\Service\AccessManager;
use App\Unit\Domain\Service\LinkChecker;
use App\Unit\Domain\Service\SlugManager;
use App\Unit\Domain\Unit;
use App\Unit\Domain\UnitRepositoryInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UnitCommandInteractor extends AbstractCommandInteractor implements CommandInterface
{
    public function __construct(
        private readonly AccessManager           $accessManager,
        private readonly LinkChecker             $linkChecker,
        private readonly SlugManager             $slugManager,
        private readonly UnitEntityProvider      $unitEntityProvider,
        private readonly UnitRepositoryInterface $repository
    )
    {
        $this->existsException = new UnitAlreadyExistsException('');
        $this->accessDeniedException = new UnitAccessDeniedException();
        $this->createCommand = new UnitCreateCommand();
        $this->updateCommand = new UnitUpdateCommand();
        $this->entityName = Unit::class;

        parent::__construct(
            $this->entityName,
            $this->accessManager,
            $this->linkChecker,
            $this->unitEntityProvider,
            $this->repository,
            $this->accessDeniedException,
            $this->existsException,
            $this->createCommand,
            $this->updateCommand
        );

    }
    protected function getAdditionalValues(UserInterface $user, ?Entity $entity, $request): array
    {
        $slug = null;

        if (is_null($entity))
            $slug = $this->slugManager->createSlug($request->getName(), $this->existsException);
        elseif ($entity instanceof Unit)
            $slug = $this->slugManager->updateSlug($entity, $request->getName(), $this->existsException);

        return compact('slug');
    }
}
