<?php

namespace App\Category\Application;

use App\Category\Application\Query\CategoryDetails;
use App\Category\Application\Query\CategoryItem;
use App\Category\Application\Query\CategoryQueryInteractor;
use App\Shared\Application\EntityHelperInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class CategoryHelper implements EntityHelperInterface
{
    public function __construct(
        private readonly CategoryQueryInteractor $categoryQueryInteractor,
        private readonly CategoryEntityProvider $categoryEntityProvider
    )
    {
    }

    public function getInstanceName(): string
    {
        return 'Category';
    }

    public function getNewDetails(): CategoryDetails
    {
        return new CategoryDetails();
    }

    public function getCountOfNotDeletedEntities(): int
    {
        return count($this->categoryEntityProvider->getNotDeletedEntitiesSortedByName());
    }

    public function getRequestOptions(UserInterface $user): array
    {
        $categories = $this->categoryQueryInteractor->getAll($user)->getItems();
        array_unshift($categories, new CategoryItem());

        return compact('categories');
    }
}
