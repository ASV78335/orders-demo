<?php

namespace App\Category\Application\Command;

use App\Shared\Application\Command\DTOCreateInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Uuid;

class CategoryCreateCommand implements DTOCreateInterface
{
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

    #[Uuid]
    private ?string $parent = null;


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
     * get parent Category Uuid
     */
    public function getParent(): ?string
    {
        return $this->parent;
    }

    /**
     * set $parent Category Uuid
     */
    public function setParent(?string $parentUuid): self
    {
        $this->parent = $parentUuid;

        return $this;
    }
}
