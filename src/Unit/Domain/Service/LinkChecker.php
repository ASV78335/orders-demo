<?php

namespace App\Unit\Domain\Service;

use App\OrderEntry\Application\OrderEntryEntityProvider;
use App\PriceListEntry\Application\PriceListEntryEntityProvider;
use App\Product\Application\ProductEntityProvider;
use App\Unit\Domain\Exception\UnitIsUsedException;
use App\Unit\Domain\Unit;

class LinkChecker
{
    public function __construct(
        private readonly OrderEntryEntityProvider $orderEntryEntityProvider,
        private readonly PriceListEntryEntityProvider $priceListEntryEntityProvider,
        private readonly ProductEntityProvider $productEntityProvider
    )
    {
    }
    public function check(Unit $unit): bool
    {
        $notDeletedRelatedProducts = $this->productEntityProvider->getNotDeletedEntitiesByField('baseUnit', $unit);
        $notDeletedRelatedPriceListEntries = $this->priceListEntryEntityProvider->getNotDeletedEntitiesByField('unit', $unit);
        $notDeletedRelatedOrderEntries = $this->orderEntryEntityProvider->getNotDeletedEntitiesByField('unit', $unit);

        $count = count($notDeletedRelatedProducts) + count($notDeletedRelatedPriceListEntries) + count($notDeletedRelatedOrderEntries);
        if ($count > 0) throw new UnitIsUsedException($count);

        return true;
    }
}
