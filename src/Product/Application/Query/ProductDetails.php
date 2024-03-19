<?php

namespace App\Product\Application\Query;

use App\Category\Application\Query\CategoryItem;
use App\Shared\Application\Query\DTODetailsInterface;
use App\Unit\Application\Query\UnitItem;

class ProductDetails implements DTODetailsInterface
{
    private ?string $uuid = null;
    private ?string $name = null;
    private ?string $slug = null;
    private ?string $description = null;
    private ?string $code = null;
    private ?CategoryItem $category = null;
    private ?UnitItem $baseUnit = null;


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

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return CategoryItem|null
     */
    public function getCategory(): ?CategoryItem
    {
        return $this->category;
    }

    /**
     * @param CategoryItem|null $categoryItem
     * @return ProductDetails
     */
    public function setCategory(?CategoryItem $categoryItem): self
    {
        $this->category = $categoryItem;

        return $this;
    }

    /**
     * @return UnitItem|null
     */
    public function getBaseUnit(): ?UnitItem
    {
        return $this->baseUnit;
    }

    /**
     * @param UnitItem|null $unitItem
     * @return ProductDetails
     */
    public function setBaseUnit(?UnitItem $unitItem): self
    {
        $this->baseUnit = $unitItem;

        return $this;
    }

}
