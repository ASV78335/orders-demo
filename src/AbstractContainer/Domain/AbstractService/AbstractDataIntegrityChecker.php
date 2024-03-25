<?php

namespace App\AbstractContainer\Domain\AbstractService;

use App\AbstractContainer\Domain\Entity;
use Symfony\Component\Security\Core\User\UserInterface;

abstract class AbstractDataIntegrityChecker
{
    abstract public function check(UserInterface $user, ?Entity $entity, $request): bool;
}
