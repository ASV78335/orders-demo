<?php

namespace App\Tests\Category\Domain\Service;

use App\Category\Domain\Exception\CategoryIsUsedException;
use App\Category\Domain\Service\LinkChecker;
use App\Shared\Application\EntityProvider;
use App\Tests\AbstractTestCase;
use App\Tests\MockUtils;

class LinkCheckerTest extends AbstractTestCase
{
    private readonly EntityProvider $entityProvider;

    protected function setUp(): void
    {
        parent::setUp();

        $this->entityProvider = $this->createMock(EntityProvider::class);
    }

    public function testCheck(): void
    {
        $category = MockUtils::createCategory();

        $this->entityProvider->expects($this->any())
            ->method('getNotDeletedEntitiesByField')
            ->with('App\Product\Domain\Product', 'category', $category)
            ->willReturn([]);

        $this->assertTrue($this->createService()->check($category));
    }

    public function testCheckCategoryIsUsedException(): void
    {
        $this->expectException(CategoryIsUsedException::class);

        $category = MockUtils::createCategory();
        $unit = MockUtils::createUnit();
        $product = MockUtils::createProduct($category, $unit);

        $this->entityProvider->expects($this->any())
            ->method('getNotDeletedEntitiesByField')
            ->with('App\Product\Domain\Product', 'category', $category)
            ->willReturn([$product]);

        $this->createService()->check($category);
    }

    private function createService(): LinkChecker
    {
        return new LinkChecker($this->entityProvider);
    }
}
