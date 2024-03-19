<?php

namespace App\Tests\Category\Domain\Service;

use App\Category\Domain\Exception\CategoryAlreadyExistsException;
use App\Category\Domain\Service\SlugManager;
use App\Category\Domain\ValueObject\CategoryUuid;
use App\Category\Infrastructure\CategoryRepository;
use App\Tests\AbstractTestCase;
use App\Tests\MockUtils;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\String\UnicodeString;

class SlugManagerTest extends AbstractTestCase
{
    private readonly CategoryRepository $repository;
    private readonly SluggerInterface $slugger;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->createMock(CategoryRepository::class);
        $this->slugger = $this->createMock(SluggerInterface::class);
    }

    public function testCreateSlug(): void
    {
        $category = MockUtils::createCategory();
        $this->slugger->expects($this->once())
            ->method('slug')
            ->with($category->getName())
            ->willReturn(new UnicodeString('Test-category'));

        $this->repository->expects($this->once())
            ->method('existBySlug')
            ->with('Test-category')
            ->willReturn(false);

        $slug = $this->createSlugManager()->createSlug($category->getName());
        $this->assertEquals('Test-category', $slug);
    }

    public function testCreateSlugCategoryAlreadyExistsException(): void
    {
        $this->expectException(CategoryAlreadyExistsException::class);

        $category = MockUtils::createCategory();

        $this->slugger->expects($this->once())
            ->method('slug')
            ->with($category->getName())
            ->willReturn(new UnicodeString('Test-category'));

        $this->repository->expects($this->once())
            ->method('existBySlug')
            ->with('Test-category')
            ->willReturn(true);

        $this->repository->expects($this->once())
            ->method('getUuidBySlug')
            ->with('Test-category')
            ->willReturn($category->getUuid());

        $this->createSlugManager()->createSlug($category->getName());
    }

    public function testUpdateSlug(): void
    {
        $category = MockUtils::createCategory();

        $this->slugger->expects($this->once())
            ->method('slug')
            ->with($category->getName())
            ->willReturn(new UnicodeString('Test-category'));

        $this->repository->expects($this->once())
            ->method('existBySlug')
            ->with('Test-category')
            ->willReturn(false);

        $slug = $this->createSlugManager()->updateSlug($category, $category->getName());
        $this->assertEquals('Test-category', $slug);
    }

    public function testUpdateSlugCategoryAlreadyExistsException(): void
    {
        $this->expectException(CategoryAlreadyExistsException::class);

        $uuid = new CategoryUuid();
        $category = MockUtils::createCategory();

        $this->slugger->expects($this->once())
            ->method('slug')
            ->with($category->getName())
            ->willReturn(new UnicodeString('Test-category'));

        $this->repository->expects($this->once())
            ->method('existBySlug')
            ->with('Test-category')
            ->willReturn(true);

        $this->repository->expects($this->once())
            ->method('getUuidBySlug')
            ->with('Test-category')
            ->willReturn($uuid);

        $this->createSlugManager()->updateSlug($category, $category->getName());
    }


    private function createSlugManager(): SlugManager
    {
        return new SlugManager($this->repository, $this->slugger);
    }

}
