<?php

namespace App\Tests\Product\Domain\Service;

use App\Product\Application\ProductEntityProvider;
use App\Product\Domain\Exception\ProductIsUsedException;
use App\Product\Domain\Service\LinkChecker;
use App\Shared\Application\EntityProvider;
use App\Tests\AbstractTestCase;
use App\Tests\MockUtils;

class LinkCheckerTest extends AbstractTestCase
{
    private readonly EntityProvider $entityProvider;
    private readonly ProductEntityProvider $productEntityProvider;

    protected function setUp(): void
    {
        parent::setUp();

        $this->entityProvider = $this->createMock(EntityProvider::class);
        $this->productEntityProvider = $this->createMock(ProductEntityProvider::class);
    }

    public function testCheck(): void
    {
        $category = MockUtils::createCategory();
        $unit = MockUtils::createUnit();
        $product = MockUtils::createProduct($category, $unit);

        $this->entityProvider->expects($this->any())
            ->method('getNotDeletedEntitiesByField')
            ->will($this->returnValueMap([
                ['App\Entity\PriceListEntry', 'product', $product, []],
                ['App\Entity\RecordEntry', 'product', $product, []]
            ]));

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
        $record = MockUtils::createRecord($person, $shop);
        $recordEntry = MockUtils::createRecordEntry($record, $product, $unit, $person);

        $this->entityProvider->expects($this->any())
            ->method('getNotDeletedEntitiesByField')
            ->will($this->returnValueMap([
                ['App\Entity\PriceListEntry', 'product', $product, []],
                ['App\Entity\RecordEntry', 'product', $product, [$recordEntry]]
            ]));

        $this->createService()->check($product);
    }

    private function createService(): LinkChecker
    {
        return new LinkChecker(
            $this->entityProvider,
            $this->productEntityProvider
        );
    }
}
