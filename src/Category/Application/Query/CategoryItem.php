<?php

namespace App\Category\Application\Query;

use App\Shared\Application\Query\DTOItemInterface;

class CategoryItem implements DTOItemInterface
{
    private ?string $uuid = null;
    private ?string $name = null;
    private ?string $slug = null;
    private ?string $description = null;
    private ?string $code = null;
    private ?string $parentName = null;


    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function setUuid(?string $uuid): self
    {
        $this->uuid = $uuid;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getParentName(): ?string
    {
        return $this->parentName;
    }

    public function setParentName(?string $parentName): self
    {
        $this->parentName = $parentName;

        return $this;
    }
}