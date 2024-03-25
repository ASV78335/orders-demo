<?php

namespace App\Tests\Category\Infrastructure;

use App\Category\Domain\Category;
use App\Category\Infrastructure\CategoryRepository;
use App\Tests\AbstractRepositoryTest;
use App\Tests\MockUtils;
use Symfony\Component\Uid\Uuid;

class CategoryRepositoryTest extends AbstractRepositoryTest
{
    const CATEGORY_SLUG = 'Test-category';

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = new CategoryRepository($this->em);
    }

    public function testGetByUuid()
    {
        $category = $this->createCategory();

        $this->assertEquals($category, $this->repository->getByUuid($category->getUuid()->getStringValue()));
    }

    public function testGetUuidBySlug()
    {
        $category = $this->createCategory();

        $this->assertEquals($category->getUuid(), $this->repository->getUuidBySlug(self::CATEGORY_SLUG));
    }

    public function testExistByUuidReturnsFalse()
    {
        $this->assertFalse($this->repository->existByUuid(Uuid::v4()->toRfc4122()));
    }

    public function testExistByUuidReturnsTrue()
    {
        $category = $this->createCategory();

        $this->assertTrue($this->repository->existByUuid($category->getUuid()->getStringValue()));
    }

    public function testExistBySlugReturnsFalse()
    {
        $this->assertFalse($this->repository->existBySlug('Test-slug'));
    }

    public function testExistBySlugReturnsTrue()
    {
        $category = $this->createCategory();

        $this->assertTrue($this->repository->existBySlug(self::CATEGORY_SLUG));
    }

    public function testGetNotDeletedSortedByName()
    {
        $category1 = MockUtils::createCategory();
        $this->setPropertyValue($category1, 'name', 'Хлеб');
        $this->setPropertyValue($category1, 'slug', 'bread');

        $category2 = MockUtils::createCategory();
        $this->setPropertyValue($category2, 'name', 'Батоны');
        $this->setPropertyValue($category2, 'slug', 'loaf');

        $category3 = MockUtils::createCategory();
        $this->setPropertyValue($category3, 'name', 'Пряники');
        $this->setPropertyValue($category3, 'slug', 'gingerbread');
        $category3->makeDeleted();

        foreach ([$category1, $category2, $category3] as $element) {
            $this->em->persist($element);
        }
        $this->em->flush();

        $names = array_map(
            fn (Category $category) => $category->getName(),
            $this->repository->getNotDeletedSortedByName()
        );

        $this->assertEquals(['Батоны', 'Хлеб'], $names);
    }

    public function testGetNotDeletedByPageSortedByName()
    {
        $category1 = MockUtils::createCategory();
        $this->setPropertyValue($category1, 'name', 'Хлеб');
        $this->setPropertyValue($category1, 'slug', 'bread');

        $category2 = MockUtils::createCategory();
        $this->setPropertyValue($category2, 'name', 'Батоны');
        $this->setPropertyValue($category2, 'slug', 'loaf');

        $category3 = MockUtils::createCategory();
        $this->setPropertyValue($category3, 'name', 'Пряники');
        $this->setPropertyValue($category3, 'slug', 'gingerbread');

        foreach ([$category1, $category2, $category3] as $element) {
            $this->em->persist($element);
        }
        $this->em->flush();

        $this->assertEquals([$category2, $category3], $this->repository->getNotDeletedByPageSortedByName(0, 2));
    }

    private function createCategory(): Category
    {
        $category = MockUtils::createCategory();
        $this->em->persist($category);
        $this->em->flush();

        return $category;
    }
}
