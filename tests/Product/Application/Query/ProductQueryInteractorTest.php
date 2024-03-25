<?php

namespace App\Tests\Product\Application\Query;

use App\Product\Application\ProductEntityProvider;
use App\Product\Application\Query\ProductList;
use App\Product\Application\Query\ProductQueryInteractor;
use App\Product\Domain\Exception\ProductAccessDeniedException;
use App\Product\Domain\Exception\ProductNotFoundException;
use App\Product\Domain\Service\AccessManager;
use App\Tests\AbstractTestCase;
use App\Tests\MockUtils;
use Symfony\Component\Uid\Uuid;

class ProductQueryInteractorTest extends AbstractTestCase
{
    private readonly AccessManager $accessManager;
    private readonly ProductEntityProvider $provider;

    protected function setUp(): void
    {
        parent::setUp();

        $this->accessManager = $this->createMock(AccessManager::class);
        $this->provider = $this->createMock(ProductEntityProvider::class);
    }

    public function testGetAll(): void
    {
        $entities = $this->createEntities();

        $this->accessManager->expects($this->once())
            ->method('canView')
            ->with($entities['person'])
            ->willReturn(true);

        $this->provider->expects($this->once())
            ->method('getNotDeletedEntities')
            ->willReturn([$entities['product']]);

        $interactor = $this->createInteractor();

        $expected = new ProductList([MockUtils::createProductItem()->setUuid($entities['product']->getUuid()->getStringValue())]);

        $this->assertEquals($expected, $interactor->getAll($entities['person']));
    }

    public function testGetAllWithEmptyData(): void
    {
        $entities = $this->createEntities();

        $this->provider->expects($this->once())
            ->method('getNotDeletedEntities')
            ->willReturn([]);

        $interactor = $this->createInteractor();

        $expected = new ProductList([]);

        $this->assertEquals($expected, $interactor->getAll($entities['person']));
    }

    public function testGetAllProductAccessDeniedException(): void
    {
        $this->expectException(ProductAccessDeniedException::class);

        $entities = $this->createEntities();

        $this->provider->expects($this->once())
            ->method('getNotDeletedEntities')
            ->willReturn([$entities['product']]);

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
            ->willReturn([$entities['product']]);

        $interactor = $this->createInteractor();

        $expected = new ProductList([MockUtils::createProductItem()->setUuid($entities['product']->getUuid()->getStringValue())]);

        $this->assertEquals($expected, $interactor->getSelection($entities['person'], $start, $count));
    }

    public function testGetSelectionWithEmptyData(): void
    {
        $entities = $this->createEntities();
        $start = 0;
        $count = 5;

        $this->provider->expects($this->once())
            ->method('getNotDeletedEntitiesByPage')
            ->with($start, $count)
            ->willReturn([]);

        $interactor = $this->createInteractor();

        $expected = new ProductList([]);

        $this->assertEquals($expected, $interactor->getSelection($entities['person'], $start, $count));
    }

    public function testGetSelectionProductAccessDeniedException(): void
    {
        $this->expectException(ProductAccessDeniedException::class);

        $entities = $this->createEntities();
        $start = 0;
        $count = 5;

        $this->provider->expects($this->once())
            ->method('getNotDeletedEntitiesByPage')
            ->with($start, $count)
            ->willReturn([$entities['product']]);

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
            ->with($entities['product']->getUuid()->getStringValue())
            ->willReturn($entities['product']);

        $interactor = $this->createInteractor();

        $expected = (MockUtils::createProductItem())
            ->setUuid($entities['product']->getUuid()->getStringValue());

        $this->assertEquals($expected, $interactor->getItem($entities['person'], $entities['product']->getUuid()->getStringValue()));
    }

    public function testGetItemProductNotFoundException(): void
    {
        $this->expectException(ProductNotFoundException::class);

        $entities = $this->createEntities();
        $uuid = Uuid::v4()->toRfc4122();

        $this->provider->expects($this->once())
            ->method('getEntityByUuid')
            ->with($uuid)
            ->will($this->throwException(new ProductNotFoundException()));

        $this->createInteractor()->getItem($entities['person'], $uuid);
    }

    public function testGetItemProductAccessDeniedException(): void
    {
        $this->expectException(ProductAccessDeniedException::class);

        $entities = $this->createEntities();

        $this->provider->expects($this->once())
            ->method('getEntityByUuid')
            ->with($entities['product']->getUuid()->getStringValue())
            ->willReturn($entities['product']);

        $this->accessManager->expects($this->once())
            ->method('canView')
            ->with($entities['person'], $entities['product'])
            ->willReturn(false);

        $this->createInteractor()->getItem($entities['person'], $entities['product']->getUuid()->getStringValue());
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
            ->with($entities['product']->getUuid()->getStringValue())
            ->willReturn($entities['product']);

        $interactor = $this->createInteractor();

        $expected = (MockUtils::createProductDetails(
            MockUtils::createCategoryItem()->setUuid($entities['category']->getUuid()->getStringValue()),
            MockUtils::createUnitItem()->setUuid($entities['unit']->getUuid()->getStringValue())
            ))
            ->setUuid($entities['product']->getUuid()->getStringValue());

        $this->assertEquals($expected, $interactor->getDetails($entities['person'], $entities['product']->getUuid()->getStringValue()));
    }

    public function testGetDetailsProductNotFoundException(): void
    {
        $this->expectException(ProductNotFoundException::class);

        $entities = $this->createEntities();
        $uuid = Uuid::v4()->toRfc4122();

        $this->provider->expects($this->once())
            ->method('getEntityByUuid')
            ->with($uuid)
            ->will($this->throwException(new ProductNotFoundException()));

        $this->createInteractor()->getDetails($entities['person'], $uuid);
    }

    public function testGetDetailsProductAccessDeniedException(): void
    {
        $this->expectException(ProductAccessDeniedException::class);

        $entities = $this->createEntities();

        $this->accessManager->expects($this->once())
            ->method('canView')
            ->with($entities['person'])
            ->willReturn(false);

        $this->createInteractor()->getDetails($entities['person'], $entities['product']->getUuid()->getStringValue());
    }

    private function createEntities(): array
    {
        $category = MockUtils::createCategory();
        $unit = MockUtils::createUnit();
        $product = MockUtils::createProduct($category, $unit);
        $person = MockUtils::createPerson();

        return ['category' => $category, 'unit' => $unit, 'person' => $person, 'product' => $product];
    }

    private function createInteractor(): ProductQueryInteractor
    {
        return new ProductQueryInteractor(
            $this->accessManager,
            $this->provider
        );
    }

}
