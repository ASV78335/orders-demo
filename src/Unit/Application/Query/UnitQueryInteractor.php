<?php

namespace App\Unit\Application\Query;

use App\Shared\Application\Query\QueryInterface;
use App\Unit\Application\UnitEntityProvider;
use App\Unit\Domain\Exception\UnitAccessDeniedException;
use App\Unit\Domain\Service\AccessManager;
use App\Unit\Domain\Unit;
use Symfony\Component\Security\Core\User\UserInterface;

class UnitQueryInteractor implements QueryInterface
{
    public const UNITS_PER_PAGE = 10;

    public function __construct(
        private readonly AccessManager $accessManager,
        private readonly UnitEntityProvider $unitEntityProvider
    )
    {
    }

    public function getAll(UserInterface $user): UnitList
    {
        if (!$this->accessManager->canView($user)) throw new UnitAccessDeniedException();

        $units = $this->unitEntityProvider->getNotDeletedEntitiesSortedByName();
        if (empty($units)) return new UnitList([]);

        return new UnitList(array_map(
            fn (Unit $unit) => $unit->toResponseItem(), $units
        ));
    }

    public function getSelection(UserInterface $user, int $offset, ?int $count): UnitList
    {
        if (!$this->accessManager->canView($user)) throw new UnitAccessDeniedException();

        $count = $count ?? self::UNITS_PER_PAGE;
        $units = $this->unitEntityProvider->getNotDeletedEntitiesByPage($offset, $count);
        if (empty($units)) return new UnitList([]);

        return new UnitList(array_map(
            fn (Unit $unit) => $unit->toResponseItem(), $units
        ));
    }

    public function getItem($user, $uuid): UnitItem
    {
        if (!$this->accessManager->canView($user)) throw new UnitAccessDeniedException();

        $unit = $this->unitEntityProvider->getEntityByUuid($uuid);

        return $unit->toResponseItem();
    }

    public function getDetails($user, string $uuid): UnitDetails
    {
        if (!$this->accessManager->canView($user)) throw new UnitAccessDeniedException();

        $unit = $this->unitEntityProvider->getEntityByUuid($uuid);

        return $unit->toResponseDetails();
    }
}
