<?php

namespace App\Shared\Application\Query;

interface DTODetailsInterface
{
    public function getUuid(): ?string;

    public function setUuid(?string $uuid): self;

    public function getName(): ?string;

    public function setName(?string $name): self;
}