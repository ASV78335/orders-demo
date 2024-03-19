<?php

namespace App\Unit\Domain\ValueObject;

use App\Shared\Domain\ValueObject\EntityUuidTrait;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class UnitUuid
{
    use EntityUuidTrait;
}
