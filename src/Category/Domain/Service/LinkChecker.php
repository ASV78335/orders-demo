<?php

namespace App\Category\Domain\Service;

use App\Category\Domain\Category;
use App\Category\Domain\Exception\CategoryIsUsedException;
use App\Product\Application\ProductEntityProvider;

class LinkChecker
{
    public function __construct(
        private readonly ProductEntityProvider $productEntityProvider
    )
    {
    }
    public function check(Category $category): bool
    {
        $notDeletedRelatedProducts = $this->productEntityProvider->getNotDeletedEntitiesByField('category', $category);
        if (count($notDeletedRelatedProducts) > 0) throw new CategoryIsUsedException(count($notDeletedRelatedProducts));

        return true;
    }
}
