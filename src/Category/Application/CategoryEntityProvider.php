<?php

namespace App\Category\Application;

use App\Category\Domain\Category;
use App\Category\Domain\CategoryRepositoryInterface;
use App\Category\Domain\Exception\CategoryNotFoundException;
use App\Shared\Application\BaseEntityProvider;
use App\Shared\Application\EntityProviderInterface;

class CategoryEntityProvider extends BaseEntityProvider implements EntityProviderInterface
{
    public function __construct(
        private readonly CategoryRepositoryInterface $repository
    )
    {
        parent::__construct($this->repository);
    }

    public function getEntityByUuid(string $uuid): Category
    {
        if (!$this->repository->existByUuid($uuid)) throw new CategoryNotFoundException();

        return $this->repository->getByUuid($uuid);
    }
}
