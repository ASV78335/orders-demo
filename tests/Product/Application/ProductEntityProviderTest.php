<?php

namespace App\Tests\Product\Application;

use App\Product\Application\ProductEntityProvider;
use App\Product\Domain\Exception\ProductNotFoundException;
use App\Product\Infrastructure\ProductRepository;
use App\Tests\AbstractTestCase;
use App\Tests\MockUtils;

class ProductEntityProviderTest extends AbstractTestCase
{
    private readonly ProductRepository $productRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->productRepository = $this->createMock(ProductRepository::class);
    }

    public function testGetEntityByUuid(): void
    {
        $entities = $this->createEntities();

        $this->productRepository->expects($this->once())
            ->method('existByUuid')
            ->with($entities['product']->getUuid()->getStringValue())
            ->willReturn(true);

        $this->productRepository->expects($this->once())
            ->method('getByUuid')
            ->with($entities['product']->getUuid()->getStringValue())
            ->willReturn($entities['product']);

        $this->assertEquals($entities['product'], $this->createProvider()->getEntityByUuid($entities['product']->getUuid()->getStringValue()));
    }

    public function testGetEntityByUuidCategoryNotFoundException(): void
    {
        $this->expectException(ProductNotFoundException::class);

        $entities = $this->createEntities();

        $this->productRepository->expects($this->once())
            ->method('existByUuid')
            ->with($entities['product']->getUuid()->getStringValue())
            ->willReturn(false);

        $this->createProvider()->getEntityByUuid($entities['product']->getUuid()->getStringValue());
    }

    public function testGetNotDeletedEntitiesSortedByName(): void
    {
        $entities = $this->createEntities();

        $this->productRepository->expects($this->once())
            ->method('getNotDeletedSortedByName')
            ->willReturn([$entities['product']]);

        $this->assertEquals([$entities['product']], $this->createProvider()->getNotDeletedEntitiesSortedByName());
    }

    public function testGetNotDeletedEntitiesByPage(): void
    {
        $entities = $this->createEntities();
        $offset = 0;
        $count = 5;

        $this->productRepository->expects($this->once())
            ->method('getNotDeletedByPage')
            ->with($offset, $count)
            ->willReturn([$entities['product']]);

        $this->assertEquals([$entities['product']], $this->createProvider()->getNotDeletedEntitiesByPage($offset, $count));
    }



    private function createEntities(): array
    {
        $category = MockUtils::createCategory();
        $unit = MockUtils::createUnit();
        $person = MockUtils::createPerson();
        $product = MockUtils::createProduct($category, $unit);

        return compact('category', 'person', 'product', 'unit');
    }

    private function createProvider(): ProductEntityProvider
    {
        return new ProductEntityProvider(
            $this->productRepository
        );
    }
}
