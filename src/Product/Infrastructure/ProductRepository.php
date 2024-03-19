<?php

namespace App\Product\Infrastructure;

use App\Product\Domain\Product;
use App\Product\Domain\ProductRepositoryInterface;
use App\Shared\Infrastructure\BaseRepository;
use Doctrine\ORM\EntityManagerInterface;

class ProductRepository extends BaseRepository implements ProductRepositoryInterface
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
        parent::__construct($this->em, Product::class);
    }
}
