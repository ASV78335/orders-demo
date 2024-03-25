<?php

namespace App\Tests\Unit\Application\Command;

use App\Product\Application\Command\ProductCreateCommand;
use App\Product\Application\Command\ProductUpdateCommand;
use App\Shared\Application\Exception\RequestParsingException;
use App\Tests\AbstractTestCase;
use App\Tests\MockUtils;
use App\Unit\Application\Command\UnitCommandInteractor;
use App\Unit\Application\Command\UnitCreateCommand;
use App\Unit\Application\Command\UnitUpdateCommand;
use App\Unit\Application\Query\UnitItem;
use App\Unit\Application\UnitEntityProvider;
use App\Unit\Domain\Exception\UnitAccessDeniedException;
use App\Unit\Domain\Service\AccessManager;
use App\Unit\Domain\Service\LinkChecker;
use App\Unit\Domain\Service\SlugManager;
use App\Unit\Infrastructure\UnitRepository;

class UnitCommandInteractorTest extends AbstractTestCase
{
    private readonly AccessManager $accessManager;
    private readonly LinkChecker $linkChecker;
    private readonly SlugManager $slugManager;
    private readonly UnitEntityProvider $entityProvider;
    private readonly UnitRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->accessManager = $this->createMock(AccessManager::class);
        $this->linkChecker = $this->createMock(LinkChecker::class);
        $this->slugManager = $this->createMock(SlugManager::class);
        $this->entityProvider = $this->createMock(UnitEntityProvider::class);
        $this->repository = $this->createMock(UnitRepository::class);
    }

    public function testCreate(): void
    {
        $entities = $this->createEntities();

        $request = (new UnitCreateCommand())
            ->setName('Test unit')
            ->setDescription('Test unit description')
            ->setCode('1100')
        ;

        $this->accessManager->expects($this->once())
            ->method('canCreate')
            ->with($entities['person'])
            ->willReturn(true);

        $this->entityProvider->expects($this->any())
            ->method('getEntityByUuid')
            ->with($entities['unit']->getUuid()->getStringValue())
            ->willReturn($entities['unit']);

        $this->slugManager->expects($this->once())
            ->method('createSlug')
            ->with($request->getName())
            ->willReturn('Test-unit');

        $this->repository->expects($this->once())
            ->method('save');

        $response =  $this->createInteractor()->create($entities['person'], $request);
        $this->assertInstanceOf(UnitItem::class, $response);
    }

    public function testCreateUnitAccessDeniedException(): void
    {
        $this->expectException(UnitAccessDeniedException::class);

        $entities = $this->createEntities();

        $request = (new UnitCreateCommand())
            ->setName('Test unit')
            ->setDescription('Test unit description')
            ->setCode('1100')
        ;

        $this->accessManager->expects($this->once())
            ->method('canCreate')
            ->with($entities['person'])
            ->willReturn(false);

        $this->createInteractor()->create($entities['person'], $request);
    }

    public function testCreateRequestParsingException(): void
    {
        $this->expectException(RequestParsingException::class);

        $entities = $this->createEntities();

        $request = (new ProductCreateCommand())
            ->setName('Test product')
            ->setDescription('Test product description')
            ->setCode('1100')
        ;

        $this->createInteractor()->create($entities['person'], $request);
    }

    public function testUpdate()
    {
        $entities = $this->createEntities();

        $request = (new UnitUpdateCommand())
            ->setName('Test unit')
            ->setDescription('Test unit description')
            ->setCode('1100')
        ;

        $this->accessManager->expects($this->once())
            ->method('canEdit')
            ->with($entities['person'])
            ->willReturn(true);

        $this->entityProvider->expects($this->any())
            ->method('getEntityByUuid')
            ->with($entities['unit']->getUuid()->getStringValue())
            ->willReturn($entities['unit']);

        $this->slugManager->expects($this->once())
            ->method('updateSlug')
            ->with($entities['unit'], $request->getName())
            ->willReturn('Test-unit');

        $this->repository->expects($this->once())
            ->method('save')
            ->with($entities['unit']);

        $response =  $this->createInteractor()->update($entities['person'], $request, $entities['unit']->getUuid()->getStringValue());
        $this->assertInstanceOf(UnitItem::class, $response);
    }

    public function testUpdateUnitAccessDeniedException(): void
    {
        $this->expectException(UnitAccessDeniedException::class);

        $entities = $this->createEntities();

        $request = (new UnitUpdateCommand())
            ->setName('Test unit')
            ->setDescription('Test unit description')
            ->setCode('1100')
        ;

        $this->accessManager->expects($this->once())
            ->method('canEdit')
            ->with($entities['person'])
            ->willReturn(false);

        $this->createInteractor()->update($entities['person'], $request, $entities['unit']->getUuid()->getStringValue());
    }

    public function testUpdateRequestParsingException(): void
    {
        $this->expectException(RequestParsingException::class);

        $entities = $this->createEntities();

        $request = (new ProductUpdateCommand())
            ->setName('Test product')
            ->setDescription('Test product description')
            ->setCode('1100')
        ;

        $this->createInteractor()->update($entities['person'], $request, $entities['unit']->getUuid()->getStringValue());
    }

    public function testDelete()
    {
        $entities = $this->createEntities();

        $this->accessManager->expects($this->once())
            ->method('canEdit')
            ->with($entities['person'])
            ->willReturn(true);

        $this->entityProvider->expects($this->once())
            ->method('getEntityByUuid')
            ->with($entities['unit']->getUuid()->getStringValue())
            ->willReturn($entities['unit']);

        $this->linkChecker->expects($this->once())
            ->method('check')
            ->willReturn(true);

        $this->repository->expects($this->once())
            ->method('save')
            ->with($entities['unit']);

        $this->createInteractor()->delete($entities['person'], $entities['unit']->getUuid()->getStringValue());
    }

    public function testDeleteUnitAccessDeniedException(): void
    {
        $this->expectException(UnitAccessDeniedException::class);

        $entities = $this->createEntities();

        $this->accessManager->expects($this->once())
            ->method('canEdit')
            ->with($entities['person'])
            ->willReturn(false);

        $this->createInteractor()->delete($entities['person'], $entities['unit']->getUuid()->getStringValue());
    }

    private function createEntities(): array
    {
        $unit = MockUtils::createUnit();
        $person = MockUtils::createPerson();

        return compact('unit', 'person');
    }

    private function createInteractor(): UnitCommandInteractor
    {
        return new UnitCommandInteractor(
            $this->accessManager,
            $this->linkChecker,
            $this->slugManager,
            $this->entityProvider,
            $this->repository
        );
    }
}
