<?php

namespace App\Product\Domain\Service;

use App\Product\Domain\Exception\ProductAlreadyExistsException;
use App\Product\Domain\Product;
use App\Product\Domain\ProductRepositoryInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class SlugManager
{
    public function __construct(
        private readonly ProductRepositoryInterface $productRepository,
        private readonly SluggerInterface $slugger
    )
    {
    }

    public function createSlug(string $name): string
    {
        $slug = $this->slugger->slug($name);
        if ($this->productRepository->existBySlug($slug->toString())) throw new ProductAlreadyExistsException();
        return $slug;
    }

    public function updateSlug(Product $product, string $name): string
    {
        $slug = $this->slugger->slug($name);
        if ($this->productRepository->existBySlug($slug->toString())) {
            $existingUuid = $this->productRepository->getUuidBySlug($slug->toString());
            if ($existingUuid !== $product->getUuid()) throw new ProductAlreadyExistsException();
        }
        return $slug;
    }
}
