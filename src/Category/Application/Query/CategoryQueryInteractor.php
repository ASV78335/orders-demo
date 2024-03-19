<?php

namespace App\Category\Application\Query;

use App\Category\Application\CategoryEntityProvider;
use App\Category\Domain\Category;
use App\Category\Domain\Exception\CategoryAccessDeniedException;
use App\Category\Domain\Service\AccessManager;
use App\Shared\Application\Query\QueryInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class CategoryQueryInteractor implements QueryInterface
{
    public const CATEGORIES_PER_PAGE = 10;

    public function __construct(
        private readonly AccessManager $accessManager,
        private readonly CategoryEntityProvider $categoryEntityProvider
    )
    {
    }

    public function getAll(UserInterface $user): CategoryList
    {
        if (!$this->accessManager->canView($user)) throw new CategoryAccessDeniedException();

        $categories = $this->categoryEntityProvider->getNotDeletedEntitiesSortedByName();
        if (empty($categories)) return new CategoryList([]);

        return new CategoryList(array_map(
            fn (Category $category) => $category->toResponseItem(), $categories
        ));
    }

    public function getSelection(UserInterface $user, int $offset, ?int $count): CategoryList
    {
        if (!$this->accessManager->canView($user)) throw new CategoryAccessDeniedException();

        $count = $count ?? self::CATEGORIES_PER_PAGE;
        $categories = $this->categoryEntityProvider->getNotDeletedEntitiesByPage($offset, $count);
        if (empty($categories)) return new CategoryList([]);

        return new CategoryList(array_map(
            fn (Category $category) => $category->toResponseItem(), $categories
        ));
    }

    public function getItem($user, $uuid): CategoryItem
    {
        if (!$this->accessManager->canView($user)) throw new CategoryAccessDeniedException();

        $category = $this->categoryEntityProvider->getEntityByUuid($uuid);

        return $category->toResponseItem();
    }

    public function getDetails($user, string $uuid): CategoryDetails
    {
        if (!$this->accessManager->canView($user)) throw new CategoryAccessDeniedException();

        $category = $this->categoryEntityProvider->getEntityByUuid($uuid);

        return $category->toResponseDetails();
    }
}
