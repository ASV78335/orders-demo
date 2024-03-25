<?php

namespace App\Unit\Application\Query;

use App\AbstractContainer\Application\Query\AbstractQueryInteractor;
use App\Shared\Application\Query\QueryInterface;
use App\Unit\Application\UnitEntityProvider;
use App\Unit\Domain\Exception\UnitAccessDeniedException;
use App\Unit\Domain\Service\AccessManager;

class UnitQueryInteractor extends AbstractQueryInteractor implements QueryInterface
{
    public function __construct(
        private readonly AccessManager      $accessManager,
        private readonly UnitEntityProvider $unitEntityProvider
    )
    {
        $this->accessDeniedException = new UnitAccessDeniedException();
        $this->DTOList = new UnitList([]);

        parent::__construct(
            $this->accessManager,
            $this->unitEntityProvider,
            $this->accessDeniedException,
            $this->DTOList
        );
    }
}
