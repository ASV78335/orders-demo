<?php

namespace App\Product\Domain\ValueObject;

use App\Shared\Domain\ValueObject\EntityUuidTrait;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class ProductUuid
{
    use EntityUuidTrait;
}
