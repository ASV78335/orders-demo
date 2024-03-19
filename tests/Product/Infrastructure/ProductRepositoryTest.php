<?php

namespace App\Tests\Product\Infrastructure;

use App\Product\Domain\Product;
use App\Product\Infrastructure\ProductRepository;
use App\Tests\AbstractRepositoryTest;
use App\Tests\MockUtils;
use Symfony\Component\Uid\Uuid;

class ProductRepositoryTest extends AbstractRepositoryTest
{
    const PRODUCT_SLUG = 'Test-product';
    private ProductRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = new ProductRepository($this->em);
    }

    public function testGetByUuid()
    {
        $entities = $this->createEntities();

        $this->assertEquals($entities['product'], $this->repository->getByUuid($entities['product']->getUuid()->getStringValue()));
    }

    public function testGetUuidBySlug()
    {
        $entities = $this->createEntities();

        $this->assertEquals($entities['product']->getUuid(), $this->repository->getUuidBySlug(self::PRODUCT_SLUG));
    }

    public function testExistByUuidReturnsFalse()
    {
        $this->assertFalse($this->repository->existByUuid(Uuid::v4()->toRfc4122()));
    }

    public function testExistByUuidReturnsTrue()
    {
        $entities = $this->createEntities();

        $this->assertTrue($this->repository->existByUuid($entities['product']->getUuid()->getStringValue()));
    }

    public function testExistBySlugReturnsFalse()
    {
        $this->assertFalse($this->repository->existBySlug('Test-slug'));
    }

    public function testExistBySlugReturnsTrue()
    {
        $entities = $this->createEntities();

        $this->assertTrue($this->repository->existBySlug(self::PRODUCT_SLUG));
    }

    public function testGetNotDeletedSortedByName()
    {
        $entities = $this->createEntities();
        $entities['product']->makeDeleted();

        $product1 = MockUtils::createProduct($entities['category'], $entities['unit']);
        $this->setPropertyValue($product1, 'name', 'Хлеб белый');
        $this->setPropertyValue($product1, 'slug', 'bread');

        $product2 = MockUtils::createProduct($entities['category'], $entities['unit']);
        $this->setPropertyValue($product2, 'name', 'Батон нарезной');
        $this->setPropertyValue($product2, 'slug', 'loaf');

        $product3 = MockUtils::createProduct($entities['category'], $entities['unit']);
        $this->setPropertyValue($product3, 'name', 'Пряники северные');
        $this->setPropertyValue($product3, 'slug', 'gingerbread');
        $product3->makeDeleted();

        foreach ([$product1, $product2, $product3] as $element) {
            $this->em->persist($element);
        }
        $this->em->flush();

        $names = array_map(
            fn (Product $product) => $product->getName(),
            $this->repository->getNotDeletedSortedByName()
        );

        $this->assertEquals(['Батон нарезной', 'Хлеб белый'], $names);
    }

    public function testGetNotDeletedByPage()
    {
        $entities = $this->createEntities();
        $entities['product']->makeDeleted();

        $product1 = MockUtils::createProduct($entities['category'], $entities['unit']);
        $this->setPropertyValue($product1, 'name', 'Хлеб белый');
        $this->setPropertyValue($product1, 'slug', 'bread');

        $product2 = MockUtils::createProduct($entities['category'], $entities['unit']);
        $this->setPropertyValue($product2, 'name', 'Батон нарезной');
        $this->setPropertyValue($product2, 'slug', 'loaf');

        $product3 = MockUtils::createProduct($entities['category'], $entities['unit']);
        $this->setPropertyValue($product3, 'name', 'Пряники северные');
        $this->setPropertyValue($product3, 'slug', 'gingerbread');

        foreach ([$product1, $product2, $product3] as $element) {
            $this->em->persist($element);
        }
        $this->em->flush();

        $this->assertEquals([$product2, $product3], $this->repository->getNotDeletedByPage(0, 2));
    }

    private function createEntities(): array
    {
        $category = MockUtils::createCategory();
        $unit = MockUtils::createUnit();
        $product = MockUtils::createProduct($category, $unit);
        foreach ([$category, $unit, $product] as $element) {
            $this->em->persist($element);
        }
        $this->em->flush();

        return compact('category', 'unit', 'product');
    }
}