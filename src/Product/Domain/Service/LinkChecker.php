<?php

namespace App\Product\Domain\Service;

use App\OrderEntry\Application\OrderEntryEntityProvider;
use App\PriceListEntry\Application\PriceListEntryEntityProvider;
use App\Product\Domain\Exception\ProductIsUsedException;
use App\Product\Domain\Product;

class LinkChecker
{
    public function __construct(
        private readonly OrderEntryEntityProvider $orderEntryEntityProvider,
        private readonly PriceListEntryEntityProvider $priceListEntryEntityProvider
    )
    {
    }
    public function check(Product $product): bool
    {
        $usedPriceListEntries = $this->priceListEntryEntityProvider
            ->getNotDeletedEntitiesByField('product', $product);
        $countOfUses = $usedPriceListEntries ? count($usedPriceListEntries) : 0;

        $usedOrderEntries = $this->orderEntryEntityProvider
            ->getNotDeletedEntitiesByField('product', $product);
        $countOfUses = $usedOrderEntries ? $countOfUses + count($usedOrderEntries) : $countOfUses;

        if ($countOfUses > 0) throw new ProductIsUsedException($countOfUses);

        return true;
    }
}
