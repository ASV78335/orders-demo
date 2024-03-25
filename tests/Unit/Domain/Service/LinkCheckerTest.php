<?php

namespace App\Tests\Unit\Domain\Service;

use App\Shared\Application\EntityProvider;
use App\Tests\AbstractTestCase;
use App\Tests\MockUtils;
use App\Unit\Domain\Exception\UnitIsUsedException;
use App\Unit\Domain\Service\LinkChecker;

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
        $unit = MockUtils::createUnit();

        $this->entityProvider->expects($this->any())
            ->method('getNotDeletedEntitiesByField')
            ->willReturnMap([
                ['App\Entity\PriceListEntry', 'unit', $unit, []],
                ['App\Product\Domain\Product', 'baseUnit', $unit, []],
                ['App\Entity\RecordEntry', 'unit', $unit, []]
            ]);

        $this->assertTrue($this->createService()->check($unit));
    }

    public function testCheckUnitIsUsedException(): void
    {
        $this->expectException(UnitIsUsedException::class);

        $category = MockUtils::createCategory();
        $unit = MockUtils::createUnit();
        $product = MockUtils::createProduct($category, $unit);

        $this->entityProvider->expects($this->any())
            ->method('getNotDeletedEntitiesByField')
            ->willReturnMap([
                ['App\Entity\PriceListEntry', 'unit', $unit, []],
                ['App\Product\Domain\Product', 'baseUnit', $unit, [$product]],
                ['App\Entity\RecordEntry', 'unit', $unit, []]
            ]);

        $this->createService()->check($unit);
    }

    private function createService(): LinkChecker
    {
        return new LinkChecker($this->entityProvider);
    }
}
