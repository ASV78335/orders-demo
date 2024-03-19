<?php

namespace App\Unit\Infrastructure;

use App\Shared\Infrastructure\BaseRepository;
use App\Unit\Domain\Unit;
use App\Unit\Domain\UnitRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class UnitRepository extends BaseRepository implements UnitRepositoryInterface
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
        parent::__construct($this->em, Unit::class);
    }
}
