<?php

namespace App\Unit\Domain\Service;

use App\Unit\Domain\Exception\UnitAlreadyExistsException;
use App\Unit\Domain\Unit;
use App\Unit\Domain\UnitRepositoryInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class SlugManager
{
    public function __construct(
        private readonly UnitRepositoryInterface $unitRepository,
        private readonly SluggerInterface $slugger
    )
    {
    }

    public function createSlug(string $name): string
    {
        $slug = $this->slugger->slug($name);
        if ($this->unitRepository->existBySlug($slug->toString()))
            throw new UnitAlreadyExistsException($this->unitRepository->getUuidBySlug($slug)->getStringValue());
        return $slug;
    }

    public function updateSlug(Unit $unit, string $name): string
    {
        $slug = $this->slugger->slug($name);
        if ($this->unitRepository->existBySlug($slug->toString())) {
            $existingUuid = $this->unitRepository->getUuidBySlug($slug->toString());
            if ($existingUuid !== $unit->getUuid())
                throw new UnitAlreadyExistsException($existingUuid->getStringValue());
        }
        return $slug;
    }
}
