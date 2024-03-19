<?php

namespace App\Tests\Unit\Domain\Service;

use App\Unit\Domain\Exception\UnitAlreadyExistsException;
use App\Unit\Domain\Service\SlugManager;
use App\Unit\Domain\ValueObject\UnitUuid;
use App\Unit\Infrastructure\UnitRepository;
use App\Tests\AbstractTestCase;
use App\Tests\MockUtils;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\String\UnicodeString;

class SlugManagerTest extends AbstractTestCase
{
    private readonly UnitRepository $repository;
    private readonly SluggerInterface $slugger;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->createMock(UnitRepository::class);
        $this->slugger = $this->createMock(SluggerInterface::class);
    }

    public function testCreateSlug(): void
    {
        $unit = MockUtils::createUnit();
        $this->slugger->expects($this->once())
            ->method('slug')
            ->with($unit->getName())
            ->willReturn(new UnicodeString('Test-unit'));

        $this->repository->expects($this->once())
            ->method('existBySlug')
            ->with('Test-unit')
            ->willReturn(false);

        $slug = $this->createSlugManager()->createSlug($unit->getName());
        $this->assertEquals('Test-unit', $slug);
    }

    public function testCreateSlugUnitAlreadyExistsException(): void
    {
        $this->expectException(UnitAlreadyExistsException::class);

        $unit = MockUtils::createUnit();

        $this->slugger->expects($this->once())
            ->method('slug')
            ->with($unit->getName())
            ->willReturn(new UnicodeString('Test-unit'));

        $this->repository->expects($this->once())
            ->method('existBySlug')
            ->with('Test-unit')
            ->willReturn(true);

        $this->repository->expects($this->once())
            ->method('getUuidBySlug')
            ->with('Test-unit')
            ->willReturn($unit->getUuid());

        $this->createSlugManager()->createSlug($unit->getName());
    }

    public function testUpdateSlug(): void
    {
        $unit = MockUtils::createUnit();

        $this->slugger->expects($this->once())
            ->method('slug')
            ->with($unit->getName())
            ->willReturn(new UnicodeString('Test-unit'));

        $this->repository->expects($this->once())
            ->method('existBySlug')
            ->with('Test-unit')
            ->willReturn(false);

        $slug = $this->createSlugManager()->updateSlug($unit, $unit->getName());
        $this->assertEquals('Test-unit', $slug);
    }

    public function testUpdateSlugUnitAlreadyExistsException(): void
    {
        $this->expectException(UnitAlreadyExistsException::class);

        $uuid = new UnitUuid();
        $unit = MockUtils::createUnit();

        $this->slugger->expects($this->once())
            ->method('slug')
            ->with($unit->getName())
            ->willReturn(new UnicodeString('Test-unit'));

        $this->repository->expects($this->once())
            ->method('existBySlug')
            ->with('Test-unit')
            ->willReturn(true);

        $this->repository->expects($this->once())
            ->method('getUuidBySlug')
            ->with('Test-unit')
            ->willReturn($uuid);

        $this->createSlugManager()->updateSlug($unit, $unit->getName());
    }


    private function createSlugManager(): SlugManager
    {
        return new SlugManager($this->repository, $this->slugger);
    }

}
