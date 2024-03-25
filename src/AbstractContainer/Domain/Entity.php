<?php

namespace App\AbstractContainer\Domain;

use App\Shared\Application\Command\DTOCreateInterface;
use App\Shared\Application\Command\DTOUpdateInterface;

abstract class Entity
{
    abstract public static function fromCreateRequest(DTOCreateInterface $model, array $additionalValues);

    abstract public static function fromUpdateRequest(Entity $entity, DTOUpdateInterface $model, array $additionalValues);

    abstract public function toResponseItem();

    abstract public function toResponseDetails();

    abstract public function makeDeleted(): void;
}
