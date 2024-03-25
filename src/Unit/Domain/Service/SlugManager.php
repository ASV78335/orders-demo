<?php

namespace App\Unit\Domain\Service;

use App\AbstractContainer\Domain\AbstractService\AbstractSlugManager;
use App\Unit\Domain\UnitRepositoryInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class SlugManager extends AbstractSlugManager
{
    public function __construct(
        UnitRepositoryInterface $repository,
        SluggerInterface $slugger
    )
    {
        parent::__construct($repository, $slugger);
    }
}

