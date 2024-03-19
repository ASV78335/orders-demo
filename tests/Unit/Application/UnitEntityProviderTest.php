<?php

namespace App\Tests\Unit\Application;

use App\Tests\AbstractTestCase;
use App\Tests\MockUtils;
use App\Unit\Application\UnitEntityProvider;
use App\Unit\Domain\Exception\UnitNotFoundException;
use App\Unit\Infrastructure\UnitRepository;

class UnitEntityProviderTest extends AbstractTestCase
{
    private readonly UnitRepository $unitRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->unitRepository = $this->createMock(UnitRepository::class);
    }

    public function testGetEntityByUuid(): void
    {
        $unit = MockUtils::createUnit();

        $this->unitRepository->expects($this->once())
            ->method('existByUuid')
            ->with($unit->getUuid()->getStringValue())
            ->willReturn(true);

        $this->unitRepository->expects($this->once())
            ->method('getByUuid')
            ->with($unit->getUuid()->getStringValue())
            ->willReturn($unit);

        $this->assertEquals($unit, $this->createProvider()->getEntityByUuid($unit->getUuid()->getStringValue()));
    }

    public function testGetEntityByUuidUnitNotFoundException(): void
    {
        $this->expectException(UnitNotFoundException::class);

        $unit = MockUtils::createUnit();

        $this->unitRepository->expects($this->once())
            ->method('existByUuid')
            ->with($unit->getUuid()->getStringValue())
            ->willReturn(false);

        $this->createProvider()->getEntityByUuid($unit->getUuid()->getStringValue());
    }

    public function testGetNotDeletedEntitiesSortedByName(): void
    {
        $unit = MockUtils::createUnit();

        $this->unitRepository->expects($this->once())
            ->method('getNotDeletedSortedByName')
            ->willReturn([$unit]);

        $this->assertEquals([$unit], $this->createProvider()->getNotDeletedEntitiesSortedByName());
    }

    public function testGetNotDeletedEntitiesByPage(): void
    {
        $unit = MockUtils::createUnit();
        $offset = 0;
        $count = 5;

        $this->unitRepository->expects($this->once())
            ->method('getNotDeletedByPage')
            ->with($offset, $count)
            ->willReturn([$unit]);

        $this->assertEquals([$unit], $this->createProvider()->getNotDeletedEntitiesByPage($offset, $count));
    }


    private function createProvider(): UnitEntityProvider
    {
        return new UnitEntityProvider(
            $this->unitRepository
        );
    }
}
