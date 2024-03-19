<?php

namespace App\Tests\Category\Application\Command;

use App\Category\Application\CategoryEntityProvider;
use App\Category\Application\Command\CategoryCommandInteractor;
use App\Category\Application\Command\CategoryCreateCommand;
use App\Category\Application\Command\CategoryUpdateCommand;
use App\Category\Application\Query\CategoryItem;
use App\Category\Domain\Exception\CategoryAccessDeniedException;
use App\Category\Domain\Service\AccessManager;
use App\Category\Domain\Service\LinkChecker;
use App\Category\Domain\Service\SlugManager;
use App\Category\Infrastructure\CategoryRepository;
use App\Product\Application\Command\ProductCreateCommand;
use App\Product\Application\Command\ProductUpdateCommand;
use App\Shared\Application\Exception\RequestParsingException;
use App\Tests\AbstractTestCase;
use App\Tests\MockUtils;

class CategoryCommandInteractorTest extends AbstractTestCase
{
    private readonly AccessManager     $accessManager;
    private readonly CategoryEntityProvider    $entityProvider;
    private readonly CategoryRepository $repository;
    private readonly LinkChecker       $linkChecker;
    private readonly SlugManager       $slugManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->accessManager = $this->createMock(AccessManager::class);
        $this->entityProvider = $this->createMock(CategoryEntityProvider::class);
        $this->repository = $this->createMock(CategoryRepository::class);
        $this->linkChecker = $this->createMock(LinkChecker::class);
        $this->slugManager = $this->createMock(SlugManager::class);
    }

    public function testCreate(): void
    {
        $entities = $this->createEntities();

        $request = (new CategoryCreateCommand())
            ->setName('Test category')
            ->setDescription('Test category description')
            ->setCode('1100')
            ->setParent($entities['category']->getUuid()->getStringValue())
        ;

        $this->accessManager->expects($this->once())
            ->method('canEdit')
            ->with($entities['person'])
            ->willReturn(true);

        $this->entityProvider->expects($this->any())
            ->method('getEntityByUuid')
            ->with($entities['category']->getUuid()->getStringValue())
            ->willReturn($entities['category']);

        $this->slugManager->expects($this->once())
            ->method('createSlug')
            ->with($request->getName())
            ->willReturn('Test-category');

        $this->repository->expects($this->once())
            ->method('save');

        $response =  $this->createInteractor()->create($entities['person'], $request);
        $this->assertInstanceOf(CategoryItem::class, $response);
    }

    public function testCreateCategoryAccessDeniedException(): void
    {
        $this->expectException(CategoryAccessDeniedException::class);

        $entities = $this->createEntities();

        $request = (new CategoryCreateCommand())
            ->setName('Test category')
            ->setDescription('Test category description')
            ->setCode('1100')
            ->setParent($entities['category']->getUuid()->getStringValue())
        ;

        $this->accessManager->expects($this->once())
            ->method('canEdit')
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

        $this->accessManager->expects($this->once())
            ->method('canEdit')
            ->with($entities['person'])
            ->willReturn(true);

        $this->createInteractor()->create($entities['person'], $request);
    }

    public function testUpdate()
    {
        $entities = $this->createEntities();

        $request = (new CategoryUpdateCommand())
            ->setName('Test category')
            ->setDescription('Test category description')
            ->setCode('1100')
            ->setParent(null)
        ;

        $this->accessManager->expects($this->once())
            ->method('canEdit')
            ->with($entities['person'])
            ->willReturn(true);

        $this->entityProvider->expects($this->any())
            ->method('getEntityByUuid')
            ->with($entities['category']->getUuid()->getStringValue())
            ->willReturn($entities['category']);

        $this->slugManager->expects($this->once())
            ->method('updateSlug')
            ->with($entities['category'], $request->getName())
            ->willReturn('Test-category');

        $this->repository->expects($this->once())
            ->method('save')
            ->with($entities['category']);

        $response =  $this->createInteractor()->update($entities['person'], $request, $entities['category']->getUuid()->getStringValue());
        $this->assertInstanceOf(CategoryItem::class, $response);
    }

    public function testUpdateCategoryAccessDeniedException(): void
    {
        $this->expectException(CategoryAccessDeniedException::class);

        $entities = $this->createEntities();

        $request = (new CategoryUpdateCommand())
            ->setName('Test category')
            ->setDescription('Test category description')
            ->setCode('1100')
            ->setParent(null)
        ;

        $this->accessManager->expects($this->once())
            ->method('canEdit')
            ->with($entities['person'])
            ->willReturn(false);

        $this->createInteractor()->update($entities['person'], $request, $entities['category']->getUuid()->getStringValue());
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

        $this->accessManager->expects($this->once())
            ->method('canEdit')
            ->with($entities['person'])
            ->willReturn(true);

        $this->createInteractor()->update($entities['person'], $request, $entities['category']->getUuid()->getStringValue());
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
            ->with($entities['category']->getUuid()->getStringValue())
            ->willReturn($entities['category']);

        $this->linkChecker->expects($this->once())
            ->method('check')
            ->willReturn(true);

        $this->repository->expects($this->once())
            ->method('save')
            ->with($entities['category']);

        $this->createInteractor()->delete($entities['person'], $entities['category']->getUuid()->getStringValue());
    }

    public function testDeleteCategoryAccessDeniedException(): void
    {
        $this->expectException(CategoryAccessDeniedException::class);

        $entities = $this->createEntities();

        $this->accessManager->expects($this->once())
            ->method('canEdit')
            ->with($entities['person'])
            ->willReturn(false);

        $this->createInteractor()->delete($entities['person'], $entities['category']->getUuid()->getStringValue());
    }

    private function createEntities(): array
    {
        $category = MockUtils::createCategory();
        $person = MockUtils::createPerson();

        return compact('category', 'person');
    }

    private function createInteractor(): CategoryCommandInteractor
    {
        return new CategoryCommandInteractor(
            $this->accessManager,
            $this->entityProvider,
            $this->repository,
            $this->linkChecker,
            $this->slugManager
        );
    }
}
