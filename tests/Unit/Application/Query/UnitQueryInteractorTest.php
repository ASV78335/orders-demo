<?php

namespace App\Tests\Unit\Application\Query;

use App\Tests\AbstractTestCase;
use App\Tests\MockUtils;
use App\Unit\Application\Query\UnitList;
use App\Unit\Application\Query\UnitQueryInteractor;
use App\Unit\Application\UnitEntityProvider;
use App\Unit\Domain\Exception\UnitAccessDeniedException;
use App\Unit\Domain\Exception\UnitNotFoundException;
use App\Unit\Domain\Service\AccessManager;
use Symfony\Component\Uid\Uuid;

class UnitQueryInteractorTest extends AbstractTestCase
{
    private readonly AccessManager $accessManager;
    private readonly UnitEntityProvider $provider;

    protected function setUp(): void
    {
        parent::setUp();

        $this->accessManager = $this->createMock(AccessManager::class);
        $this->provider = $this->createMock(UnitEntityProvider::class);
    }

    public function testGetAll(): void
    {
        $entities = $this->createEntities();

        $this->accessManager->expects($this->once())
            ->method('canView')
            ->with($entities['person'])
            ->willReturn(true);

        $this->provider->expects($this->once())
            ->method('getNotDeletedEntitiesSortedByName')
            ->willReturn([$entities['unit']]);

        $interactor = $this->createInteractor();

        $expected = new UnitList([MockUtils::createUnitItem()->setUuid($entities['unit']->getUuid()->getStringValue())]);

        $this->assertEquals($expected, $interactor->getAll($entities['person']));
    }

    public function testGetAllWithEmptyData(): void
    {
        $entities = $this->createEntities();

        $this->accessManager->expects($this->once())
            ->method('canView')
            ->with($entities['person'])
            ->willReturn(true);

        $this->provider->expects($this->once())
            ->method('getNotDeletedEntitiesSortedByName')
            ->willReturn([]);

        $interactor = $this->createInteractor();

        $expected = new UnitList([]);

        $this->assertEquals($expected, $interactor->getAll($entities['person']));
    }

    public function testGetAllUnitAccessDeniedException(): void
    {
        $this->expectException(UnitAccessDeniedException::class);

        $entities = $this->createEntities();

        $this->accessManager->expects($this->once())
            ->method('canView')
            ->with($entities['person'])
            ->willReturn(false);

        $this->createInteractor()->getAll($entities['person']);
    }

    public function testGetSelection(): void
    {
        $entities = $this->createEntities();
        $start = 0;
        $count = 5;

        $this->accessManager->expects($this->once())
            ->method('canView')
            ->with($entities['person'])
            ->willReturn(true);

        $this->provider->expects($this->once())
            ->method('getNotDeletedEntitiesByPage')
            ->with($start, $count)
            ->willReturn([$entities['unit']]);

        $interactor = $this->createInteractor();

        $expected = new UnitList([MockUtils::createUnitItem()->setUuid($entities['unit']->getUuid()->getStringValue())]);

        $this->assertEquals($expected, $interactor->getSelection($entities['person'], $start, $count));
    }

    public function testGetSelectionWithEmptyData(): void
    {
        $entities = $this->createEntities();
        $start = 0;
        $count = 5;

        $this->accessManager->expects($this->once())
            ->method('canView')
            ->with($entities['person'])
            ->willReturn(true);

        $this->provider->expects($this->once())
            ->method('getNotDeletedEntitiesByPage')
            ->with($start, $count)
            ->willReturn([]);

        $interactor = $this->createInteractor();

        $expected = new UnitList([]);

        $this->assertEquals($expected, $interactor->getSelection($entities['person'], $start, $count));
    }

    public function testGetSelectionUnitAccessDeniedException(): void
    {
        $this->expectException(UnitAccessDeniedException::class);

        $entities = $this->createEntities();
        $start = 0;
        $count = 5;

        $this->accessManager->expects($this->once())
            ->method('canView')
            ->with($entities['person'])
            ->willReturn(false);

        $this->createInteractor()->getSelection($entities['person'], $start, $count);
    }

    public function testGetItem(): void
    {
        $entities = $this->createEntities();

        $this->accessManager->expects($this->once())
            ->method('canView')
            ->with($entities['person'])
            ->willReturn(true);

        $this->provider->expects($this->once())
            ->method('getEntityByUuid')
            ->with($entities['unit']->getUuid()->getStringValue())
            ->willReturn($entities['unit']);

        $interactor = $this->createInteractor();

        $expected = (MockUtils::createUnitItem())
            ->setUuid($entities['unit']->getUuid()->getStringValue());

        $this->assertEquals($expected, $interactor->getItem($entities['person'], $entities['unit']->getUuid()->getStringValue()));
    }

    public function testGetItemUnitNotFoundException(): void
    {
        $this->expectException(UnitNotFoundException::class);

        $entities = $this->createEntities();
        $uuid = Uuid::v4()->toRfc4122();

        $this->accessManager->expects($this->once())
            ->method('canView')
            ->with($entities['person'])
            ->willReturn(true);

        $this->provider->expects($this->once())
            ->method('getEntityByUuid')
            ->with($uuid)
            ->will($this->throwException(new UnitNotFoundException()));

        $this->createInteractor()->getItem($entities['person'], $uuid);
    }

    public function testGetItemUnitAccessDeniedException(): void
    {
        $this->expectException(UnitAccessDeniedException::class);

        $entities = $this->createEntities();

        $this->accessManager->expects($this->once())
            ->method('canView')
            ->with($entities['person'])
            ->willReturn(false);

        $this->createInteractor()->getItem($entities['person'], $entities['unit']->getUuid());
    }

    public function testGetDetails(): void
    {
        $entities = $this->createEntities();

        $this->accessManager->expects($this->once())
            ->method('canView')
            ->with($entities['person'])
            ->willReturn(true);

        $this->provider->expects($this->once())
            ->method('getEntityByUuid')
            ->with($entities['unit']->getUuid()->getStringValue())
            ->willReturn($entities['unit']);

        $interactor = $this->createInteractor();

        $expected = (MockUtils::createUnitDetails())
            ->setUuid($entities['unit']->getUuid()->getStringValue());

        $this->assertEquals($expected, $interactor->getDetails($entities['person'], $entities['unit']->getUuid()->getStringValue()));
    }

    public function testGetDetailsUnitNotFoundException(): void
    {
        $this->expectException(UnitNotFoundException::class);

        $entities = $this->createEntities();
        $uuid = Uuid::v4()->toRfc4122();

        $this->accessManager->expects($this->once())
            ->method('canView')
            ->with($entities['person'])
            ->willReturn(true);

        $this->provider->expects($this->once())
            ->method('getEntityByUuid')
            ->with($uuid)
            ->will($this->throwException(new UnitNotFoundException()));

        $this->createInteractor()->getDetails($entities['person'], $uuid);
    }

    public function testGetDetailsUnitAccessDeniedException(): void
    {
        $this->expectException(UnitAccessDeniedException::class);

        $entities = $this->createEntities();

        $this->accessManager->expects($this->once())
            ->method('canView')
            ->with($entities['person'])
            ->willReturn(false);

        $this->createInteractor()->getDetails($entities['person'], $entities['unit']->getUuid()->getStringValue());
    }

    private function createEntities(): array
    {
        $unit = MockUtils::createUnit();
        $person = MockUtils::createPerson();

        return ['unit' => $unit, 'person' => $person];
    }

    private function createInteractor(): UnitQueryInteractor
    {
        return new UnitQueryInteractor(
            $this->accessManager,
            $this->provider
        );
    }

}
