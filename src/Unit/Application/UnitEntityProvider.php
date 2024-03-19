<?php

namespace App\Unit\Application;

use App\Shared\Application\BaseEntityProvider;
use App\Shared\Application\EntityProviderInterface;
use App\Unit\Domain\Unit;
use App\Unit\Domain\UnitRepositoryInterface;
use App\Unit\Domain\Exception\UnitNotFoundException;

class UnitEntityProvider extends BaseEntityProvider implements EntityProviderInterface
{
    public function __construct(
        private readonly UnitRepositoryInterface $repository
    )
    {
        parent::__construct($this->repository);
    }

    public function getEntityByUuid(string $uuid): Unit
    {
        if (!$this->repository->existByUuid($uuid)) throw new UnitNotFoundException();

        return $this->repository->getByUuid($uuid);
    }
}
