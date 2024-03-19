<?php

namespace App\Tests\Product\Application\Command;

use App\Category\Application\CategoryEntityProvider;
use App\Product\Application\Command\ProductCommandInteractor;
use App\Product\Application\Command\ProductCreateCommand;
use App\Product\Application\Command\ProductUpdateCommand;
use App\Product\Application\ProductEntityProvider;
use App\Product\Application\Query\ProductItem;
use App\Product\Domain\Exception\ProductAccessDeniedException;
use App\Product\Domain\Service\AccessManager;
use App\Product\Domain\Service\LinkChecker;
use App\Product\Domain\Service\SlugManager;
use App\Product\Infrastructure\ProductRepository;
use App\Tests\AbstractTestCase;
use App\Tests\MockUtils;
use App\Unit\Application\UnitEntityProvider;

class ProductCommandInteractorTest extends AbstractTestCase
{
    private readonly AccessManager $accessManager;
    private readonly CategoryEntityProvider $categoryEntityProvider;
    private readonly LinkChecker $linkChecker;
    private readonly ProductEntityProvider $productEntityProvider;
    private readonly ProductRepository $repository;
    private readonly SlugManager $slugManager;
    private readonly UnitEntityProvider $unitEntityProvider;

    protected function setUp(): void
    {
        parent::setUp();

        $this->accessManager = $this->createMock(AccessManager::class);
        $this->categoryEntityProvider = $this->createMock(CategoryEntityProvider::class);
        $this->linkChecker = $this->createMock(LinkChecker::class);
        $this->productEntityProvider = $this->createMock(ProductEntityProvider::class);
        $this->repository = $this->createMock(ProductRepository::class);
        $this->slugManager = $this->createMock(SlugManager::class);
        $this->unitEntityProvider = $this->createMock(UnitEntityProvider::class);
    }

    public function testCreate(): void
    {
        $entities = $this->createEntities();

        $request = (new ProductCreateCommand())
            ->setName('Test product')
            ->setDescription('Test product description')
            ->setCode('1100')
            ->setCategory($entities['category']->getUuid()->getStringValue())
            ->setBaseUnit($entities['unit']->getUuid()->getStringValue())
        ;

        $this->accessManager->expects($this->once())
            ->method('canEdit')
            ->with($entities['person'])
            ->willReturn(true);

        $this->categoryEntityProvider->expects($this->once())
            ->method('getEntityByUuid')
            ->with($entities['category']->getUuid()->getStringValue())
            ->willReturn($entities['category']);

        $this->unitEntityProvider->expects($this->any())
            ->method('getEntityByUuid')
            ->with($entities['unit']->getUuid()->getStringValue())
            ->willReturn($entities['unit']);

        $this->slugManager->expects($this->once())
            ->method('createSlug')
            ->with($request->getName())
            ->willReturn('Test-product');

        $this->repository->expects($this->once())
            ->method('save');

        $response =  $this->createInteractor()->create($entities['person'], $request);
        $this->assertInstanceOf(ProductItem::class, $response);
    }

    public function testCreateProductAccessDeniedException(): void
    {
        $this->expectException(ProductAccessDeniedException::class);

        $entities = $this->createEntities();

        $request = (new ProductCreateCommand())
            ->setName('Test product')
            ->setDescription('Test product description')
            ->setCode('1100')
            ->setCategory($entities['category']->getUuid()->getStringValue())
            ->setBaseUnit($entities['unit']->getUuid()->getStringValue())
        ;

        $this->accessManager->expects($this->once())
            ->method('canEdit')
            ->with($entities['person'])
            ->willReturn(false);

        $this->createInteractor()->create($entities['person'], $request);
    }

    public function testUpdate()
    {
        $entities = $this->createEntities();

        $request = (new ProductUpdateCommand())
            ->setName('Test product')
            ->setDescription('Test product description')
            ->setCode('1100')
            ->setCategory($entities['category']->getUuid()->getStringValue())
            ->setBaseUnit($entities['unit']->getUuid()->getStringValue())
        ;

        $this->accessManager->expects($this->once())
            ->method('canEdit')
            ->with($entities['person'])
            ->willReturn(true);

        $this->productEntityProvider->expects($this->once())
            ->method('getEntityByUuid')
            ->with($entities['product']->getUuid()->getStringValue())
            ->willReturn($entities['product']);

        $this->categoryEntityProvider->expects($this->once())
            ->method('getEntityByUuid')
            ->with($entities['category']->getUuid()->getStringValue())
            ->willReturn($entities['category']);

        $this->unitEntityProvider->expects($this->any())
            ->method('getEntityByUuid')
            ->with($entities['unit']->getUuid()->getStringValue())
            ->willReturn($entities['unit']);

        $this->slugManager->expects($this->once())
            ->method('updateSlug')
            ->with($entities['product'], $request->getName())
            ->willReturn('Test-product');

        $this->repository->expects($this->once())
            ->method('save')
            ->with($entities['product']);

        $response = $this->createInteractor()->update($entities['person'], $request, $entities['product']->getUuid()->getStringValue());
        $this->assertInstanceOf(ProductItem::class, $response);
    }

    public function testUpdateProductAccessDeniedException(): void
    {
        $this->expectException(ProductAccessDeniedException::class);

        $entities = $this->createEntities();

        $request = (new ProductUpdateCommand())
            ->setName('Test product')
            ->setDescription('Test product description')
            ->setCode('1100')
            ->setCategory($entities['category']->getUuid()->getStringValue())
            ->setBaseUnit($entities['unit']->getUuid()->getStringValue())
        ;

        $this->accessManager->expects($this->once())
            ->method('canEdit')
            ->with($entities['person'])
            ->willReturn(false);

        $this->createInteractor()->update($entities['person'], $request, $entities['product']->getUuid()->getStringValue());
    }

    public function testDelete()
    {
        $entities = $this->createEntities();

        $this->accessManager->expects($this->once())
            ->method('canEdit')
            ->with($entities['person'])
            ->willReturn(true);

        $this->productEntityProvider->expects($this->once())
            ->method('getEntityByUuid')
            ->with($entities['product']->getUuid()->getStringValue())
            ->willReturn($entities['product']);

        $this->linkChecker->expects($this->once())
            ->method('check')
            ->willReturn(true);

        $this->repository->expects($this->once())
            ->method('save')
            ->with($entities['product']);

        $this->createInteractor()->delete($entities['person'], $entities['product']->getUuid()->getStringValue());
    }

    public function testDeleteProductAccessDeniedException(): void
    {
        $this->expectException(ProductAccessDeniedException::class);

        $entities = $this->createEntities();

        $this->accessManager->expects($this->once())
            ->method('canEdit')
            ->with($entities['person'])
            ->willReturn(false);

        $this->createInteractor()->delete($entities['person'], $entities['product']->getUuid()->getStringValue());
    }

    private function createEntities(): array
    {
        $category = MockUtils::createCategory();
        $unit = MockUtils::createUnit();
        $person = MockUtils::createPerson();
        $product = MockUtils::createProduct($category, $unit);

        return compact('category', 'person', 'product', 'unit');
    }

    private function createInteractor(): ProductCommandInteractor
    {
        return new ProductCommandInteractor(
            $this->accessManager,
            $this->categoryEntityProvider,
            $this->linkChecker,
            $this->productEntityProvider,
            $this->repository,
            $this->slugManager,
            $this->unitEntityProvider
        );
    }
}
