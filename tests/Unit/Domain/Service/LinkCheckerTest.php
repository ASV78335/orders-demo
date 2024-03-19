<?php

namespace App\Tests\Unit\Domain\Service;

use App\OrderEntry\Application\OrderEntryEntityProvider;
use App\PriceListEntry\Application\PriceListEntryEntityProvider;
use App\Product\Application\ProductEntityProvider;
use App\Tests\AbstractTestCase;
use App\Tests\MockUtils;
use App\Unit\Domain\Exception\UnitIsUsedException;
use App\Unit\Domain\Service\LinkChecker;

class LinkCheckerTest extends AbstractTestCase
{
    private readonly OrderEntryEntityProvider $orderEntryEntityProvider;
    private readonly PriceListEntryEntityProvider $priceListEntryEntityProvider;
    private readonly ProductEntityProvider $productEntityProvider;

    protected function setUp(): void
    {
        parent::setUp();

        $this->orderEntryEntityProvider = $this->createMock(OrderEntryEntityProvider::class);
        $this->priceListEntryEntityProvider = $this->createMock(PriceListEntryEntityProvider::class);
        $this->productEntityProvider = $this->createMock(ProductEntityProvider::class);
    }

    public function testCheck(): void
    {
        $unit = MockUtils::createUnit();

        $this->productEntityProvider->expects($this->once())
            ->method('getNotDeletedEntitiesByField')
            ->with('baseUnit', $unit)
            ->willReturn([]);

        $this->orderEntryEntityProvider->expects($this-once())
            ->method('getNotDeletedEntitiesByField')
            ->with('unit', $unit)
            ->willReturn([]);

        $this->priceListEntryEntityProvider->expects($this-once())
            ->method('getNotDeletedEntitiesByField')
            ->with('unit', $unit)
            ->willReturn([]);

        $this->assertTrue($this->createService()->check($unit));
    }

    public function testCheckUnitIsUsedException(): void
    {
        $this->expectException(UnitIsUsedException::class);

        $category = MockUtils::createCategory();
        $unit = MockUtils::createUnit();
        $product = MockUtils::createProduct($category, $unit);

        $this->productEntityProvider->expects($this->once())
            ->method('getNotDeletedEntitiesByField')
            ->with('baseUnit', $unit)
            ->willReturn([$product]);

        $this->orderEntryEntityProvider->expects($this-once())
            ->method('getNotDeletedEntitiesByField')
            ->with('unit', $unit)
            ->willReturn([]);

        $this->priceListEntryEntityProvider->expects($this-once())
            ->method('getNotDeletedEntitiesByField')
            ->with('unit', $unit)
            ->willReturn([]);

        $this->createService()->check($unit);
    }

    private function createService(): LinkChecker
    {
        return new LinkChecker(
            $this->orderEntryEntityProvider,
            $this->priceListEntryEntityProvider,
            $this->productEntityProvider
        );
    }
}
