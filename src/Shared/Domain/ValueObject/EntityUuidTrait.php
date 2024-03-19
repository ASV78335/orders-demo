<?php

namespace App\Shared\Domain\ValueObject;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

trait EntityUuidTrait
{
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    private readonly string $uuid;

    final public function __construct(?string $uuid = null)
    {
        $this->uuid = $uuid ?? Uuid::v4()->toRfc4122();
    }

    public function getStringValue(): string
    {
        return $this->uuid;
    }

    public function getValue(): Uuid
    {
        return Uuid::fromString($this->uuid);
    }
}
