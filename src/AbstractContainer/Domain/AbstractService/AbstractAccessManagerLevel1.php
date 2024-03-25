<?php

namespace App\AbstractContainer\Domain\AbstractService;

use App\Entity\Person;

abstract class AbstractAccessManagerLevel1 extends AbstractAccessManager
{
    public function canCreate($person, $entity = null): bool
    {
        return $this->canEdit($person, $entity);
    }

    public function canEdit($person, $entity = null): bool
    {
        if (!$person instanceof Person) return false;

        $status = $person->getStatus();

        if ($status === $_ENV['ADMIN_STATUS']) return true;

        return false;
    }
}
