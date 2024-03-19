<?php

namespace App\Category\Domain\ValueObject;

use App\Shared\Domain\ValueObject\EntityUuidTrait;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class CategoryUuid
{
    use EntityUuidTrait;
}
