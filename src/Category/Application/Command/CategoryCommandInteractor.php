<?php

namespace App\Category\Application\Command;

use App\AbstractContainer\Application\Command\AbstractCommandInteractor;
use App\AbstractContainer\Domain\Entity;
use App\Category\Application\CategoryEntityProvider;
use App\Category\Domain\Category;
use App\Category\Domain\CategoryRepositoryInterface;
use App\Category\Domain\Exception\CategoryAccessDeniedException;
use App\Category\Domain\Exception\CategoryAlreadyExistsException;
use App\Category\Domain\Service\AccessManager;
use App\Category\Domain\Service\LinkChecker;
use App\Category\Domain\Service\SlugManager;
use App\Shared\Application\Command\CommandInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class CategoryCommandInteractor extends AbstractCommandInteractor implements CommandInterface
{
    public function __construct(
        private readonly AccessManager               $accessManager,
        private readonly CategoryEntityProvider      $categoryEntityProvider,
        private readonly CategoryRepositoryInterface $repository,
        private readonly LinkChecker                 $linkChecker,
        private readonly SlugManager                 $slugManager
    )
    {
        $this->existsException = new CategoryAlreadyExistsException('');
        $this->accessDeniedException = new CategoryAccessDeniedException();
        $this->createCommand = new CategoryCreateCommand();
        $this->updateCommand = new CategoryUpdateCommand();
        $this->entityName = Category::class;

        parent::__construct(
            $this->entityName,
            $this->accessManager,
            $this->linkChecker,
            $this->categoryEntityProvider,
            $this->repository,
            $this->accessDeniedException,
            $this->existsException,
            $this->createCommand,
            $this->updateCommand
        );
    }

    protected function getAdditionalValues(UserInterface $user, ?Entity $entity, $request): array
    {
        $slug = null;

        if ($request->getParent())
            $parent = $this->categoryEntityProvider->getEntityByUuid($request->getParent());
        else
            $parent = null;

        if (is_null($entity))
            $slug = $this->slugManager->createSlug($request->getName(), $this->existsException);
        elseif ($entity instanceof Category)
            $slug = $this->slugManager->updateSlug($entity, $request->getName(), $this->existsException);

        return compact('parent', 'slug');
    }
}
