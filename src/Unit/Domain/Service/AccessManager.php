<?php

namespace App\Unit\Domain\Service;

use App\Person\Domain\Person;

class AccessManager
{
    public function canEdit($person): bool
    {
        if (!$person instanceof Person) return false;

        $status = $person->getStatus();

        if ($status === $_ENV['ADMIN_STATUS']) return true;

        return false;
    }

    public function canView($person): bool
    {
        if (!$person instanceof Person) return false;

        return true;
    }
}
