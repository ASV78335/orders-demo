<?php

namespace App\Category\Application\Command;

use App\Category\Application\CategoryEntityProvider;
use App\Category\Application\Query\CategoryItem;
use App\Category\Domain\Category;
use App\Category\Domain\CategoryRepositoryInterface;
use App\Category\Domain\Exception\CategoryAccessDeniedException;
use App\Category\Domain\Service\AccessManager;
use App\Category\Domain\Service\LinkChecker;
use App\Category\Domain\Service\SlugManager;
use App\Shared\Application\Command\CommandInterface;
use App\Shared\Application\Command\DTOCreateInterface;
use App\Shared\Application\Command\DTOUpdateInterface;
use App\Shared\Application\Exception\RequestParsingException;
use Symfony\Component\Security\Core\User\UserInterface;

class CategoryCommandInteractor implements CommandInterface
{
    public function __construct(
        private readonly AccessManager $accessManager,
        private readonly CategoryEntityProvider $categoryEntityProvider,
        private readonly CategoryRepositoryInterface $repository,
        private readonly LinkChecker $linkChecker,
        private readonly SlugManager $slugManager
    )
    {
    }

    public function create(UserInterface $user, DTOCreateInterface $request): ?CategoryItem
    {
        if (!$this->accessManager->canEdit($user)) throw new CategoryAccessDeniedException();

        if (!$request instanceof CategoryCreateCommand) throw new RequestParsingException();

        if ($request->getParent())
            $parent = $this->categoryEntityProvider->getEntityByUuid($request->getParent());
        else
            $parent = null;

        $slug = $this->slugManager->createSlug($request->getName());
        $values = compact('parent', 'slug');

        $category = Category::fromCreateRequest($request, $values);
        $this->repository->save($category);
        return $category->toResponseItem();
    }

    public function update(UserInterface $user, DTOUpdateInterface $request, string $uuid): ?CategoryItem
    {
        if (!$this->accessManager->canEdit($user)) throw new CategoryAccessDeniedException();

        if (!$request instanceof CategoryUpdateCommand) throw new RequestParsingException();

        $request->setUuid($uuid);
        $category = $this->categoryEntityProvider->getEntityByUuid($uuid);

        if ($request->getParent())
            $parent = $this->categoryEntityProvider->getEntityByUuid($request->getParent());
        else
            $parent = null;

        $slug = $this->slugManager->updateSlug($category, $request->getName());
        $values = compact('parent', 'slug');

        $category = Category::fromUpdateRequest($category, $request, $values);
        $this->repository->save($category);

        return $category->toResponseItem();
    }

    public function delete(UserInterface $user, string $uuid): void
    {
        if (!$this->accessManager->canEdit($user)) throw new CategoryAccessDeniedException();

        $category = $this->categoryEntityProvider->getEntityByUuid($uuid);

        if ($this->linkChecker->check($category)) $category->makeDeleted();

        $this->repository->save($category);
    }
}
