<?php

namespace App\Product\Application;

use App\Category\Application\Query\CategoryQueryInteractor;
use App\Product\Application\Query\ProductDetails;
use App\Shared\Application\EntityHelperInterface;
use App\Unit\Application\Query\UnitQueryInteractor;
use Symfony\Component\Security\Core\User\UserInterface;

class ProductHelper implements EntityHelperInterface
{
    public function __construct(
        private readonly CategoryQueryInteractor $categoryQueryInteractor,
        private readonly ProductEntityProvider $productEntityProvider,
        private readonly UnitQueryInteractor $unitQueryInteractor
    )
    {
    }

    public function getInstanceName(): string
    {
        return 'Product';
    }

    public function getNewDetails(): ProductDetails
    {
        return new ProductDetails();
    }

    public function getCountOfNotDeletedEntities(): int
    {
        return count($this->productEntityProvider->getNotDeletedEntitiesSortedByName());
    }

    public function getRequestOptions(UserInterface $user): array
    {
        $categories = $this->categoryQueryInteractor->getAll($user)->getItems();
        $units = $this->unitQueryInteractor->getAll($user)->getItems();

        return compact('categories', 'units');
    }
}
