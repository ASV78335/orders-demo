<?php

namespace App\Unit\Application;

use App\Shared\Application\EntityHelperInterface;
use App\Unit\Application\Query\UnitDetails;
use Symfony\Component\Security\Core\User\UserInterface;

class UnitHelper implements EntityHelperInterface
{
    public function __construct(
        private readonly UnitEntityProvider $unitEntityProvider
    )
    {
    }

    public function getInstanceName(): string
    {
        return 'Unit';
    }

    public function getNewDetails(): UnitDetails
    {
        return new UnitDetails();
    }

    public function getCountOfNotDeletedEntities(): int
    {
        return count($this->unitEntityProvider->getNotDeletedEntitiesSortedByName());
    }

    public function getRequestOptions(UserInterface $user): array
    {
        return [];
    }
}
