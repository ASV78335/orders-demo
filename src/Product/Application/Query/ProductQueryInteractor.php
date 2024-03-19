<?php

namespace App\Product\Application\Query;

use App\Product\Application\ProductEntityProvider;
use App\Product\Domain\Exception\ProductAccessDeniedException;
use App\Product\Domain\Product;
use App\Product\Domain\Service\AccessManager;
use App\Shared\Application\Query\QueryInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class ProductQueryInteractor implements QueryInterface
{
    public const PRODUCTS_PER_PAGE = 10;

    public function __construct(
        private readonly AccessManager $accessManager,
        private readonly ProductEntityProvider $productEntityProvider
    )
    {
    }

    public function getAll(UserInterface $user): ProductList
    {
        if (!$this->accessManager->canView($user)) throw new ProductAccessDeniedException();

        $products = $this->productEntityProvider->getNotDeletedEntitiesSortedByName();
        if (empty($products)) return new ProductList([]);

        return new ProductList(array_map(
            fn (Product $product) => $product->toResponseItem(), $products
        ));
    }

    public function getSelection(UserInterface $user, int $offset, ?int $count): ProductList
    {
        if (!$this->accessManager->canView($user)) throw new ProductAccessDeniedException();

        $count = $count ?? self::PRODUCTS_PER_PAGE;
        $products = $this->productEntityProvider->getNotDeletedEntitiesByPage($offset, $count);
        if (empty($products)) return new ProductList([]);

        return new ProductList(array_map(
            fn (Product $product) => $product->toResponseItem(), $products
        ));
    }

    public function getItem($user, $uuid): ProductItem
    {
        if (!$this->accessManager->canView($user)) throw new ProductAccessDeniedException();

        $product = $this->productEntityProvider->getEntityByUuid($uuid);

        return $product->toResponseItem();
    }

    public function getDetails($user, string $uuid): ProductDetails
    {
        if (!$this->accessManager->canView($user)) throw new ProductAccessDeniedException();

        $product = $this->productEntityProvider->getEntityByUuid($uuid);

        return $product->toResponseDetails();
    }
}
