<?php

namespace App\AbstractContainer\Domain\AbstractService;

use App\AbstractContainer\Domain\Entity;
use App\Shared\Domain\EntityRepositoryInterface;
use App\Shared\Domain\Exception\BusinessLogicException;
use Symfony\Component\String\Slugger\SluggerInterface;

abstract class AbstractSlugManager
{
    protected EntityRepositoryInterface $repository;
    protected SluggerInterface $slugger;

    protected function __construct(EntityRepositoryInterface $repository, SluggerInterface $slugger)
    {
        $this->repository = $repository;
        $this->slugger= $slugger;
    }

    public function createSlug(string $name, BusinessLogicException $exception): string
    {
        $slug = $this->slugger->slug($name);
        if ($this->repository->existBySlug($slug->toString()))
            throw new $exception($this->repository->getUuidBySlug($slug)->getStringValue());
        return $slug;
    }

    public function updateSlug(Entity $entity, string $name, BusinessLogicException $exception): string
    {
        $slug = $this->slugger->slug($name);
        if ($this->repository->existBySlug($slug->toString())) {
            $existingUuid = $this->repository->getUuidBySlug($slug->toString());
            if ($existingUuid !== $entity->getUuid())
                throw new $exception($existingUuid->getStringValue());
        }
        return $slug;
    }
}
