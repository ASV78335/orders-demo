<?php

namespace App\Product\Application\Command;

use App\Shared\Application\Command\DTOUpdateInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Uuid;

class ProductUpdateCommand implements DTOUpdateInterface
{
    private ?string $uuid = null;

    #[NotBlank]
    private ?string $name = null;

    private ?string $description = null;

    #[Length(
        min: 2,
        max: 10,
        minMessage: 'Code must be at least {{ limit }} characters long',
        maxMessage: 'Code cannot be longer than {{ limit }} characters'
    )]
    private ?string $code = null;

    #[NotBlank]
    #[Uuid]
    private ?string $category = null;

    #[NotBlank]
    #[Uuid]
    private ?string $baseUnit = null;


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

    /**
     * get Category Uuid
     */
    public function getCategory(): ?string
    {
        return $this->category;
    }

    /**
     * set Category Uuid
     */
    public function setCategory(?string $categoryUuid): self
    {
        $this->category = $categoryUuid;

        return $this;
    }

    /**
     * get Unit Uuid
     */
    public function getBaseUnit(): ?string
    {
        return $this->baseUnit;
    }

    /**
     * set Unit Uuid
     */
    public function setBaseUnit(?string $baseUnitUuid): self
    {
        $this->baseUnit = $baseUnitUuid;

        return $this;
    }
}
