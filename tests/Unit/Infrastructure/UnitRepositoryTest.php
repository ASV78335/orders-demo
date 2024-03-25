<?php

namespace App\Tests\Unit\Infrastructure;

use App\Unit\Domain\Unit;
use App\Unit\Infrastructure\UnitRepository;
use App\Tests\AbstractRepositoryTest;
use App\Tests\MockUtils;
use Symfony\Component\Uid\Uuid;

class UnitRepositoryTest extends AbstractRepositoryTest
{
    const UNIT_SLUG = 'Test-unit';

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = new UnitRepository($this->em);
    }

    public function testGetByUuid()
    {
        $unit = $this->createUnit();

        $this->assertEquals($unit, $this->repository->getByUuid($unit->getUuid()->getStringValue()));
    }

    public function testGetUuidBySlug()
    {
        $unit = $this->createUnit();

        $this->assertEquals($unit->getUuid(), $this->repository->getUuidBySlug(self::UNIT_SLUG));
    }

    public function testExistByUuidReturnsFalse()
    {
        $this->assertFalse($this->repository->existByUuid(Uuid::v4()->toRfc4122()));
    }

    public function testExistByUuidReturnsTrue()
    {
        $unit = $this->createUnit();

        $this->assertTrue($this->repository->existByUuid($unit->getUuid()->getStringValue()));
    }

    public function testExistBySlugReturnsFalse()
    {
        $this->assertFalse($this->repository->existBySlug('Test-slug'));
    }

    public function testExistBySlugReturnsTrue()
    {
        $unit = $this->createUnit();

        $this->assertTrue($this->repository->existBySlug(self::UNIT_SLUG));
    }

    public function testGetNotDeletedSortedByName()
    {
        $unit1 = MockUtils::createUnit();
        $this->setPropertyValue($unit1, 'name', 'шт.');
        $this->setPropertyValue($unit1, 'slug', 'sht.');

        $unit2 = MockUtils::createUnit();
        $this->setPropertyValue($unit2, 'name', 'уп.');
        $this->setPropertyValue($unit2, 'slug', 'up.');

        $unit3 = MockUtils::createUnit();
        $this->setPropertyValue($unit3, 'name', 'кг');
        $this->setPropertyValue($unit3, 'slug', 'kg');
        $unit3->makeDeleted();

        foreach ([$unit1, $unit2, $unit3] as $element) {
            $this->em->persist($element);
        }
        $this->em->flush();

        $names = array_map(
            fn (Unit $unit) => $unit->getName(),
            $this->repository->getNotDeletedSortedByName()
        );

        $this->assertEquals(['уп.', 'шт.'], $names);
    }

    public function testGetNotDeletedByPageSortedByName()
    {
        $unit1 = MockUtils::createUnit();
        $this->setPropertyValue($unit1, 'name', 'шт.');
        $this->setPropertyValue($unit1, 'slug', 'sht.');

        $unit2 = MockUtils::createUnit();
        $this->setPropertyValue($unit2, 'name', 'уп.');
        $this->setPropertyValue($unit2, 'slug', 'up.');

        $unit3 = MockUtils::createUnit();
        $this->setPropertyValue($unit3, 'name', 'кг');
        $this->setPropertyValue($unit3, 'slug', 'kg');

        foreach ([$unit1, $unit2, $unit3] as $element) {
            $this->em->persist($element);
        }
        $this->em->flush();

        $this->assertEquals([$unit3, $unit2], $this->repository->getNotDeletedByPageSortedByName(0, 2));
    }

    private function createUnit(): Unit
    {
        $unit = MockUtils::createUnit();
        $this->em->persist($unit);
        $this->em->flush();

        return $unit;
    }
}
