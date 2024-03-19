<?php

namespace App\Tests\Product\Domain\Service;

use App\OrderEntry\Application\OrderEntryEntityProvider;
use App\PriceListEntry\Application\PriceListEntryEntityProvider;
use App\Product\Domain\Exception\ProductIsUsedException;
use App\Product\Domain\Service\LinkChecker;
use App\Tests\AbstractTestCase;
use App\Tests\MockUtils;

class LinkCheckerTest extends AbstractTestCase
{
    private readonly OrderEntryEntityProvider $orderEntryEntityProvider;
    private readonly PriceListEntryEntityProvider $priceListEntryEntityProvider;

    protected function setUp(): void
    {
        parent::setUp();

        $this->orderEntryEntityProvider = $this->createMock(OrderEntryEntityProvider::class);
        $this->priceListEntryEntityProvider = $this->createMock(PriceListEntryEntityProvider::class);
    }

    public function testCheck(): void
    {
        $category = MockUtils::createCategory();
        $unit = MockUtils::createUnit();
        $product = MockUtils::createProduct($category, $unit);

        $this->orderEntryEntityProvider->expects($this-once())
            ->method('getNotDeletedEntitiesByField')
            ->with('product', $product)
            ->willReturn([]);

        $this->priceListEntryEntityProvider->expects($this-once())
            ->method('getNotDeletedEntitiesByField')
            ->with('product', $product)
            ->willReturn([]);

        $this->assertTrue($this->createService()->check($product));
    }

    public function testCheckProductIsUsedException(): void
    {
        $this->expectException(ProductIsUsedException::class);

        $category = MockUtils::createCategory();
        $unit = MockUtils::createUnit();
        $product = MockUtils::createProduct($category, $unit);
        $person = MockUtils::CreatePerson();
        $contragent = MockUtils::createContragent();
        $shop = MockUtils::CreateShop(null, $contragent, null);
        $order = MockUtils::createOrder($person, $shop);
        $orderEntry = MockUtils::createOrderEntry($order, $product, $unit, $person);

        $this->orderEntryEntityProvider->expects($this-once())
            ->method('getNotDeletedEntitiesByField')
            ->with('product', $product)
            ->willReturn([]);

        $this->priceListEntryEntityProvider->expects($this-once())
            ->method('getNotDeletedEntitiesByField')
            ->with('product', $product)
            ->willReturn([$orderEntry]);

        $this->createService()->check($product);
    }

    private function createService(): LinkChecker
    {
        return new LinkChecker(
            $this->orderEntryEntityProvider,
            $this->priceListEntryEntityProvider
        );
    }
}
