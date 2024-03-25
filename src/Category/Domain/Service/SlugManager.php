<?php

namespace App\Category\Domain\Service;

use App\AbstractContainer\Domain\AbstractService\AbstractSlugManager;
use App\Category\Domain\CategoryRepositoryInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class SlugManager extends AbstractSlugManager
{
    public function __construct(
        CategoryRepositoryInterface $repository,
        SluggerInterface $slugger
    )
    {
        parent::__construct($repository, $slugger);
    }
}
