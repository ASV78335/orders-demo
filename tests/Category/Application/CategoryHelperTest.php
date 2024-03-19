<?php

namespace App\Tests\Category\Application;

use App\Category\Application\CategoryEntityProvider;
use App\Category\Application\CategoryHelper;
use App\Category\Application\Query\CategoryDetails;
use App\Category\Application\Query\CategoryItem;
use App\Category\Application\Query\CategoryList;
use App\Category\Application\Query\CategoryQueryInteractor;
use App\Tests\AbstractTestCase;
use App\Tests\MockUtils;

class CategoryHelperTest extends AbstractTestCase
{
    private readonly CategoryQueryInteractor $categoryQueryInteractor;
    private readonly CategoryEntityProvider $provider;

    protected function setUp(): void
    {
        parent::setUp();

        $this->categoryQueryInteractor = $this->createMock(CategoryQueryInteractor::class);
        $this->provider = $this->createMock(CategoryEntityProvider::class);
    }

    public function testGetInstanceName(): void
    {
        $helper = $this->createHelper();

        $this->assertEquals('Category', $helper->getInstanceName());
    }

    public function testGetNewDetails(): void
    {
        $helper = $this->createHelper();

        $this->assertEquals(new CategoryDetails(), $helper->getNewDetails());
    }

    public function testGetRequestOptions(): void
    {
        $entities = $this->createEntities();
        $categoryItem = (MockUtils::createCategoryItem())
            ->setUuid($entities['category']->getUuid()->getStringValue());

        $this->categoryQueryInteractor->expects($this->once())
            ->method('getAll')
            ->willReturn(new CategoryList([$categoryItem]));

        $expected = [
            'categories' => [new CategoryItem(), $categoryItem]
        ];

        $this->assertEquals($expected, $this->createHelper()->getRequestOptions($entities['person']));
    }

    private function createEntities(): array
    {
        $category = MockUtils::createCategory();
        $person = MockUtils::createPerson();

        return ['category' => $category, 'person' => $person];
    }

    private function createHelper(): CategoryHelper
    {
        return new CategoryHelper(
            $this->categoryQueryInteractor,
            $this->provider
        );
    }
}
