<?php

namespace App\Category\Infrastructure;

use App\Category\Domain\Category;
use App\Category\Domain\CategoryRepositoryInterface;
use App\Shared\Infrastructure\BaseRepository;
use Doctrine\ORM\EntityManagerInterface;

class CategoryRepository extends BaseRepository implements CategoryRepositoryInterface
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
        parent::__construct($this->em, Category::class);
    }
}
