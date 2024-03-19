<?php

namespace App\Product\Application;

use App\Product\Domain\Product;
use App\Product\Domain\ProductRepositoryInterface;
use App\Product\Domain\Exception\ProductNotFoundException;
use App\Shared\Application\BaseEntityProvider;
use App\Shared\Application\EntityProviderInterface;

class ProductEntityProvider extends BaseEntityProvider implements EntityProviderInterface
{
    public function __construct(
        private readonly ProductRepositoryInterface $repository
    )
    {
        parent::__construct($this->repository);
    }

    public function getEntityByUuid(string $uuid): Product
    {
        if (!$this->repository->existByUuid($uuid)) throw new ProductNotFoundException();

        return $this->repository->getByUuid($uuid);
    }
}
