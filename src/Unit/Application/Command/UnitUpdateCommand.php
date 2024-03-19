<?php

namespace App\Unit\Application\Command;

use App\Shared\Application\Command\DTOUpdateInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UnitUpdateCommand implements DTOUpdateInterface
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
}
