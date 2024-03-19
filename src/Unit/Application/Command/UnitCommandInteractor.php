<?php

namespace App\Unit\Application\Command;

use App\Shared\Application\Command\CommandInterface;
use App\Shared\Application\Command\DTOCreateInterface;
use App\Shared\Application\Command\DTOUpdateInterface;
use App\Shared\Application\Exception\RequestParsingException;
use App\Unit\Application\Query\UnitItem;
use App\Unit\Application\UnitEntityProvider;
use App\Unit\Domain\Exception\UnitAccessDeniedException;
use App\Unit\Domain\Service\AccessManager;
use App\Unit\Domain\Service\LinkChecker;
use App\Unit\Domain\Service\SlugManager;
use App\Unit\Domain\Unit;
use App\Unit\Domain\UnitRepositoryInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UnitCommandInteractor implements CommandInterface
{
    public function __construct(
        private readonly AccessManager $accessManager,
        private readonly LinkChecker $linkChecker,
        private readonly SlugManager $slugManager,
        private readonly UnitEntityProvider $unitEntityProvider,
        private readonly UnitRepositoryInterface $repository
    )
    {
    }

    public function create(UserInterface $user, DTOCreateInterface $request): ?UnitItem
    {
        if (!$this->accessManager->canEdit($user)) throw new UnitAccessDeniedException();

        if (!$request instanceof UnitCreateCommand) throw new RequestParsingException();

        $slug = $this->slugManager->createSlug($request->getName());
        $values = compact( 'slug');

        $unit = Unit::fromCreateRequest($request, $values);
        $this->repository->save($unit);
        return $unit->toResponseItem();
    }

    public function update(UserInterface $user, DTOUpdateInterface $request, string $uuid): ?UnitItem
    {
        if (!$this->accessManager->canEdit($user)) throw new UnitAccessDeniedException();

        if (!$request instanceof UnitUpdateCommand) throw new RequestParsingException();

        $request->setUuid($uuid);
        $unit = $this->unitEntityProvider->getEntityByUuid($uuid);

        $slug = $this->slugManager->updateSlug($unit, $request->getName());
        $values = compact('slug');

        $unit = Unit::fromUpdateRequest($unit, $request, $values);
        $this->repository->save($unit);

        return $unit->toResponseItem();
    }

    public function delete(UserInterface $user, string $uuid): void
    {
        if (!$this->accessManager->canEdit($user)) throw new UnitAccessDeniedException();

        $unit = $this->unitEntityProvider->getEntityByUuid($uuid);

        if ($this->linkChecker->check($unit)) $unit->makeDeleted();

        $this->repository->save($unit);
    }
}
