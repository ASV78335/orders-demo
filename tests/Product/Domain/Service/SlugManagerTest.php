<?php

namespace App\Tests\Product\Domain\Service;

use App\Product\Domain\Exception\ProductAlreadyExistsException;
use App\Product\Domain\Service\SlugManager;
use App\Product\Domain\ValueObject\ProductUuid;
use App\Product\Infrastructure\ProductRepository;
use App\Tests\AbstractTestCase;
use App\Tests\MockUtils;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\String\UnicodeString;

class SlugManagerTest extends AbstractTestCase
{
    private readonly ProductRepository $repository;
    private readonly SluggerInterface $slugger;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->createMock(ProductRepository::class);
        $this->slugger = $this->createMock(SluggerInterface::class);
    }

    public function testCreateSlug(): void
    {
        $entities = $this->createEntities();
        $this->slugger->expects($this->once())
            ->method('slug')
            ->with($entities['product']->getName())
            ->willReturn(new UnicodeString('Test-product'));

        $this->repository->expects($this->once())
            ->method('existBySlug')
            ->with('Test-product')
            ->willReturn(false);

        $slug = $this->createSlugManager()->createSlug($entities['product']->getName());
        $this->assertEquals('Test-product', $slug);
    }

    public function testCreateSlugProductAlreadyExistsException(): void
    {
        $this->expectException(ProductAlreadyExistsException::class);

        $entities = $this->createEntities();

        $this->slugger->expects($this->once())
            ->method('slug')
            ->with($entities['product']->getName())
            ->willReturn(new UnicodeString('Test-product'));

        $this->repository->expects($this->once())
            ->method('existBySlug')
            ->with('Test-product')
            ->willReturn(true);

        $this->createSlugManager()->createSlug($entities['product']->getName());
    }

    public function testUpdateSlug(): void
    {
        $entities = $this->createEntities();
        $this->slugger->expects($this->once())
            ->method('slug')
            ->with($entities['product']->getName())
            ->willReturn(new UnicodeString('Test-product'));

        $this->repository->expects($this->once())
            ->method('existBySlug')
            ->with('Test-product')
            ->willReturn(false);

        $slug = $this->createSlugManager()->updateSlug($entities['product'], $entities['product']->getName());
        $this->assertEquals('Test-product', $slug);
    }

    public function testUpdateSlugProductAlreadyExistsException(): void
    {
        $this->expectException(ProductAlreadyExistsException::class);

        $uuid = new ProductUuid();
        $entities = $this->createEntities();
        $this->slugger->expects($this->once())
            ->method('slug')
            ->with($entities['product']->getName())
            ->willReturn(new UnicodeString('Test-product'));

        $this->repository->expects($this->once())
            ->method('existBySlug')
            ->with('Test-product')
            ->willReturn(true);

        $this->repository->expects($this->once())
            ->method('getUuidBySlug')
            ->with('Test-product')
            ->willReturn($uuid);

        $this->createSlugManager()->updateSlug($entities['product'], $entities['product']->getName());

    }

    private function createEntities(): array
    {
        $category = MockUtils::createCategory();
        $unit = MockUtils::createUnit();
        $product = MockUtils::createProduct($category, $unit);

        return ['category' => $category, 'product' => $product, 'unit' => $unit];
    }

    private function createSlugManager(): SlugManager
    {
        return new SlugManager($this->repository, $this->slugger);
    }

}
