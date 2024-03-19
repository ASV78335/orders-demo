<?php

namespace App\Category\Domain\Service;

use App\Category\Domain\Category;
use App\Category\Domain\CategoryRepositoryInterface;
use App\Category\Domain\Exception\CategoryAlreadyExistsException;
use Symfony\Component\String\Slugger\SluggerInterface;

class SlugManager
{
    public function __construct(
        private readonly CategoryRepositoryInterface $categoryRepository,
        private readonly SluggerInterface $slugger
    )
    {
    }

    public function createSlug(string $name): string
    {
        $slug = $this->slugger->slug($name);
        if ($this->categoryRepository->existBySlug($slug->toString()))
            throw new CategoryAlreadyExistsException($this->categoryRepository->getUuidBySlug($slug)->getStringValue());
        return $slug;
    }

    public function updateSlug(Category $category, string $name): string
    {
        $slug = $this->slugger->slug($name);
        if ($this->categoryRepository->existBySlug($slug->toString())) {
            $existingUuid = $this->categoryRepository->getUuidBySlug($slug->toString());
            if ($existingUuid !== $category->getUuid())
                throw new CategoryAlreadyExistsException($existingUuid->getStringValue());
        }
        return $slug;
    }
}
