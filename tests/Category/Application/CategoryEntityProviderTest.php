<?php

namespace App\Tests\Category\Application;

use App\Category\Application\CategoryEntityProvider;
use App\Category\Domain\Exception\CategoryNotFoundException;
use App\Category\Infrastructure\CategoryRepository;
use App\Tests\AbstractTestCase;
use App\Tests\MockUtils;

class CategoryEntityProviderTest extends AbstractTestCase
{
    private readonly CategoryRepository $categoryRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->categoryRepository = $this->createMock(CategoryRepository::class);
    }

    public function testGetEntityByUuid(): void
    {
        $category = MockUtils::createCategory();

        $this->categoryRepository->expects($this->once())
            ->method('existByUuid')
            ->with($category->getUuid()->getStringValue())
            ->willReturn(true);

        $this->categoryRepository->expects($this->once())
            ->method('getByUuid')
            ->with($category->getUuid()->getStringValue())
            ->willReturn($category);

        $this->assertEquals($category, $this->createProvider()->getEntityByUuid($category->getUuid()->getStringValue()));
    }

    public function testGetEntityByUuidCategoryNotFoundException(): void
    {
        $this->expectException(CategoryNotFoundException::class);

        $category = MockUtils::createCategory();

        $this->categoryRepository->expects($this->once())
            ->method('existByUuid')
            ->with($category->getUuid()->getStringValue())
            ->willReturn(false);

        $this->createProvider()->getEntityByUuid($category->getUuid()->getStringValue());
    }

    public function testGetNotDeletedEntitiesSortedByName(): void
    {
        $category = MockUtils::createCategory();

        $this->categoryRepository->expects($this->once())
            ->method('getNotDeletedSortedByName')
            ->willReturn([$category]);

        $this->assertEquals([$category], $this->createProvider()->getNotDeletedEntitiesSortedByName());
    }

    public function testGetNotDeletedEntitiesByPage(): void
    {
        $category = MockUtils::createCategory();
        $offset = 0;
        $count = 5;

        $this->categoryRepository->expects($this->once())
            ->method('getNotDeletedByPage')
            ->with($offset, $count)
            ->willReturn([$category]);

        $this->assertEquals([$category], $this->createProvider()->getNotDeletedEntitiesByPage($offset, $count));
    }


    private function createProvider(): CategoryEntityProvider
    {
        return new CategoryEntityProvider(
            $this->categoryRepository
        );
    }
}
