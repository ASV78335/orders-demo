<?php

namespace App\Shared\Application\Command;

interface DTOUpdateInterface
{
    public function getUuid(): ?string;

    public function setUuid(?string $uuid): self;

    public function getName(): ?string;

    public function setName(?string $name): self;
}
