<?php

namespace App\Tests\Category\Application\Query;

use App\Category\Application\CategoryEntityProvider;
use App\Category\Application\Query\CategoryList;
use App\Category\Application\Query\CategoryQueryInteractor;
use App\Category\Domain\Exception\CategoryAccessDeniedException;
use App\Category\Domain\Exception\CategoryNotFoundException;
use App\Category\Domain\Service\AccessManager;
use App\Tests\AbstractTestCase;
use App\Tests\MockUtils;
use Symfony\Component\Uid\Uuid;

class CategoryQueryInteractorTest extends AbstractTestCase
{
    private readonly AccessManager $accessManager;
    private readonly CategoryEntityProvider $provider;

    protected function setUp(): void
    {
        parent::setUp();

        $this->accessManager = $this->createMock(AccessManager::class);
        $this->provider = $this->createMock(CategoryEntityProvider::class);
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
            ->willReturn([$entities['category']]);

        $interactor = $this->createInteractor();

        $expected = new CategoryList([MockUtils::createCategoryItem()->setUuid($entities['category']->getUuid()->getStringValue())]);

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

        $expected = new CategoryList([]);

        $this->assertEquals($expected, $interactor->getAll($entities['person']));
    }

    public function testGetAllCategoryAccessDeniedException(): void
    {
        $this->expectException(CategoryAccessDeniedException::class);

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
            ->willReturn([$entities['category']]);

        $interactor = $this->createInteractor();

        $expected = new CategoryList([MockUtils::createCategoryItem()->setUuid($entities['category']->getUuid()->getStringValue())]);

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

        $expected = new CategoryList([]);

        $this->assertEquals($expected, $interactor->getSelection($entities['person'], $start, $count));
    }

    public function testGetSelectionCategoryAccessDeniedException(): void
    {
        $this->expectException(CategoryAccessDeniedException::class);

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
            ->with($entities['category']->getUuid()->getStringValue())
            ->willReturn($entities['category']);

        $interactor = $this->createInteractor();

        $expected = (MockUtils::createCategoryItem())
            ->setUuid($entities['category']->getUuid()->getStringValue());

        $this->assertEquals($expected, $interactor->getItem($entities['person'], $entities['category']->getUuid()->getStringValue()));
    }

    public function testGetItemCategoryNotFoundException(): void
    {
        $this->expectException(CategoryNotFoundException::class);

        $entities = $this->createEntities();
        $uuid = Uuid::v4()->toRfc4122();

        $this->accessManager->expects($this->once())
            ->method('canView')
            ->with($entities['person'])
            ->willReturn(true);

        $this->provider->expects($this->once())
            ->method('getEntityByUuid')
            ->with($uuid)
            ->will($this->throwException(new CategoryNotFoundException()));

        $this->createInteractor()->getItem($entities['person'], $uuid);
    }

    public function testGetItemCategoryAccessDeniedException(): void
    {
        $this->expectException(CategoryAccessDeniedException::class);

        $entities = $this->createEntities();

        $this->accessManager->expects($this->once())
            ->method('canView')
            ->with($entities['person'])
            ->willReturn(false);

        $this->createInteractor()->getItem($entities['person'], $entities['category']->getUuid());
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
            ->with($entities['category']->getUuid()->getStringValue())
            ->willReturn($entities['category']);

        $interactor = $this->createInteractor();

        $expected = (MockUtils::createCategoryDetails(null))
            ->setUuid($entities['category']->getUuid()->getStringValue());

        $this->assertEquals($expected, $interactor->getDetails($entities['person'], $entities['category']->getUuid()->getStringValue()));
    }

    public function testGetDetailsCategoryNotFoundException(): void
    {
        $this->expectException(CategoryNotFoundException::class);

        $entities = $this->createEntities();
        $uuid = Uuid::v4()->toRfc4122();

        $this->accessManager->expects($this->once())
            ->method('canView')
            ->with($entities['person'])
            ->willReturn(true);

        $this->provider->expects($this->once())
            ->method('getEntityByUuid')
            ->with($uuid)
            ->will($this->throwException(new CategoryNotFoundException()));

        $this->createInteractor()->getDetails($entities['person'], $uuid);
    }

    public function testGetDetailsCategoryAccessDeniedException(): void
    {
        $this->expectException(CategoryAccessDeniedException::class);

        $entities = $this->createEntities();

        $this->accessManager->expects($this->once())
            ->method('canView')
            ->with($entities['person'])
            ->willReturn(false);

        $this->createInteractor()->getDetails($entities['person'], $entities['category']->getUuid()->getStringValue());
    }

    private function createEntities(): array
    {
        $category = MockUtils::createCategory();
        $person = MockUtils::createPerson();

        return ['category' => $category, 'person' => $person];
    }

    private function createInteractor(): CategoryQueryInteractor
    {
        return new CategoryQueryInteractor(
            $this->accessManager,
            $this->provider
        );
    }

}
