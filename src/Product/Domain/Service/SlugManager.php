<?php

namespace App\Product\Domain\Service;

use App\AbstractContainer\Domain\AbstractService\AbstractSlugManager;
use App\Product\Domain\ProductRepositoryInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class SlugManager extends AbstractSlugManager
{
    public function __construct(
        ProductRepositoryInterface $repository,
        SluggerInterface $slugger
    )
    {
        parent::__construct($repository, $slugger);
    }
}
