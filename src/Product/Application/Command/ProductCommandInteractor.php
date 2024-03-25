<?php

namespace App\Product\Application\Command;

use App\AbstractContainer\Application\Command\AbstractCommandInteractor;
use App\AbstractContainer\Domain\Entity;
use App\Category\Application\CategoryEntityProvider;
use App\Product\Application\ProductEntityProvider;
use App\Product\Domain\Exception\ProductAccessDeniedException;
use App\Product\Domain\Exception\ProductAlreadyExistsException;
use App\Product\Domain\Product;
use App\Product\Domain\ProductRepositoryInterface;
use App\Product\Domain\Service\AccessManager;
use App\Product\Domain\Service\LinkChecker;
use App\Product\Domain\Service\SlugManager;
use App\Shared\Application\Command\CommandInterface;
use App\Unit\Application\UnitEntityProvider;
use Symfony\Component\Security\Core\User\UserInterface;

class ProductCommandInteractor extends AbstractCommandInteractor implements CommandInterface
{
    public function __construct(
        private readonly AccessManager              $accessManager,
        private readonly CategoryEntityProvider     $categoryEntityProvider,
        private readonly LinkChecker                $linkChecker,
        private readonly ProductEntityProvider      $productEntityProvider,
        private readonly ProductRepositoryInterface $repository,
        private readonly SlugManager                $slugManager,
        private readonly UnitEntityProvider         $unitEntityProvider
    )
    {
        $this->existsException = new ProductAlreadyExistsException('');
        $this->accessDeniedException = new ProductAccessDeniedException();
        $this->createCommand = new ProductCreateCommand();
        $this->updateCommand = new ProductUpdateCommand();
        $this->entityName = Product::class;

        parent::__construct(
            $this->entityName,
            $this->accessManager,
            $this->linkChecker,
            $this->productEntityProvider,
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

        if (is_null($entity))
            $slug = $this->slugManager->createSlug($request->getName(), $this->existsException);
        elseif ($entity instanceof Product)
            $slug = $this->slugManager->updateSlug($entity, $request->getName(), $this->existsException);

        $category = $this->categoryEntityProvider->getEntityByUuid($request->getCategory());
        $baseUnit = $this->unitEntityProvider->getEntityByUuid($request->getBaseUnit());

        return compact('category', 'baseUnit', 'slug');
    }
}
