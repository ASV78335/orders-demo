<?php

namespace App\Shared\Application\Command;

interface DTOUpdateInterface
{
    public function getName(): ?string;

    public function setName(?string $name): self;
}
