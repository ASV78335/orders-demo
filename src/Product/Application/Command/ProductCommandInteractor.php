<?php

namespace App\Product\Application\Command;

use App\Category\Application\CategoryEntityProvider;
use App\Product\Application\ProductEntityProvider;
use App\Product\Application\Query\ProductItem;
use App\Product\Domain\Exception\ProductAccessDeniedException;
use App\Product\Domain\Product;
use App\Product\Domain\ProductRepositoryInterface;
use App\Product\Domain\Service\AccessManager;
use App\Product\Domain\Service\LinkChecker;
use App\Product\Domain\Service\SlugManager;
use App\Shared\Application\Command\CommandInterface;
use App\Shared\Application\Command\DTOCreateInterface;
use App\Shared\Application\Command\DTOUpdateInterface;
use App\Shared\Application\Exception\RequestParsingException;
use App\Unit\Application\UnitEntityProvider;
use Symfony\Component\Security\Core\User\UserInterface;

class ProductCommandInteractor implements CommandInterface
{
    public function __construct(
        private readonly AccessManager $accessManager,
        private readonly CategoryEntityProvider $categoryEntityProvider,
        private readonly LinkChecker $linkChecker,
        private readonly ProductEntityProvider $productEntityProvider,
        private readonly ProductRepositoryInterface $productRepository,
        private readonly SlugManager $slugManager,
        private readonly UnitEntityProvider $unitEntityProvider
    )
    {
    }

    public function create(UserInterface $user, DTOCreateInterface $request): ?ProductItem
    {
        if (!$this->accessManager->canEdit($user)) throw new ProductAccessDeniedException();

        if (!$request instanceof ProductCreateCommand) throw new RequestParsingException();

        $category = $this->categoryEntityProvider->getEntityByUuid($request->getCategory());
        $baseUnit = $this->unitEntityProvider->getEntityByUuid($request->getBaseUnit());
        $slug = $this->slugManager->createSlug($request->getName());
        $values = compact('category', 'baseUnit', 'slug');

        $product = Product::fromCreateRequest($request, $values);
        $this->productRepository->save($product);
        return $product->toResponseItem();
    }

    public function update(UserInterface $user, DTOUpdateInterface $request, string $uuid): ?ProductItem
    {
        if (!$this->accessManager->canEdit($user)) throw new ProductAccessDeniedException();

        if (!$request instanceof ProductUpdateCommand) throw new RequestParsingException();

        $request->setUuid($uuid);
        $product = $this->productEntityProvider->getEntityByUuid($uuid);

        $category = $this->categoryEntityProvider->getEntityByUuid($request->getCategory());
        $baseUnit = $this->unitEntityProvider->getEntityByUuid($request->getBaseUnit());
        $slug = $this->slugManager->updateSlug($product, $request->getName());
        $values = compact('category', 'baseUnit', 'slug');

        $product = Product::fromUpdateRequest($product, $request, $values);
        $this->productRepository->save($product);

        return $product->toResponseItem();
    }

    public function delete(UserInterface $user, string $uuid): void
    {
        if (!$this->accessManager->canEdit($user)) throw new ProductAccessDeniedException();

        $product = $this->productEntityProvider->getEntityByUuid($uuid);

        if ($this->linkChecker->check($product)) $product->makeDeleted();

        $this->productRepository->save($product);
    }
}
