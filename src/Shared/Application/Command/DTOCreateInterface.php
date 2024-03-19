<?php

namespace App\Shared\Application\Command;

interface DTOCreateInterface
{
    public function getName(): ?string;

    public function setName(?string $name): self;
}
