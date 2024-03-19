<?php

namespace App\Tests\Product\Application;

use App\Category\Application\Query\CategoryList;
use App\Category\Application\Query\CategoryQueryInteractor;
use App\Product\Application\ProductEntityProvider;
use App\Product\Application\ProductHelper;
use App\Product\Application\Query\ProductDetails;
use App\Tests\AbstractTestCase;
use App\Tests\MockUtils;
use App\Unit\Application\Query\UnitList;
use App\Unit\Application\Query\UnitQueryInteractor;

class ProductHelperTest extends AbstractTestCase
{
    private readonly CategoryQueryInteractor $categoryQueryInteractor;
    private readonly ProductEntityProvider $provider;
    private readonly UnitQueryInteractor $unitQueryInteractor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->categoryQueryInteractor = $this->createMock(CategoryQueryInteractor::class);
        $this->provider = $this->createMock(ProductEntityProvider::class);
        $this->unitQueryInteractor = $this->createMock(UnitQueryInteractor::class);
    }

    public function testGetInstanceName(): void
    {
        $helper = $this->createHelper();

        $this->assertEquals('Product', $helper->getInstanceName());
    }

    public function testGetNewDetails(): void
    {
        $helper = $this->createHelper();

        $this->assertEquals(new ProductDetails(), $helper->getNewDetails());
    }

    public function testGetRequestOptions(): void
    {
        $entities = $this->createEntities();
        $categoryItem = (MockUtils::createCategoryItem())
            ->setUuid($entities['category']->getUuid()->getStringValue());
        $unitItem = (MockUtils::createUnitItem())
            ->setUuid($entities['unit']->getUuid()->getStringValue());

        $this->categoryQueryInteractor->expects($this->once())
            ->method('getAll')
            ->willReturn(new CategoryList([$categoryItem]));

        $this->unitQueryInteractor->expects($this->once())
            ->method('getAll')
            ->willReturn(new UnitList([$unitItem]));

        $expected = [
            'categories' => [$categoryItem],
            'units' => [$unitItem]
        ];

        $this->assertEquals($expected,$this->createHelper()->getRequestOptions($entities['person']));
    }

    private function createEntities(): array
    {
        $category = MockUtils::createCategory();
        $unit = MockUtils::createUnit();
        $product = MockUtils::createProduct($category, $unit);
        $person = MockUtils::createPerson();

        return ['category' => $category, 'unit' => $unit, 'person' => $person, 'product' => $product];
    }

    private function createHelper(): ProductHelper
    {
        return new ProductHelper(
            $this->categoryQueryInteractor,
            $this->provider,
            $this->unitQueryInteractor
        );
    }
}
