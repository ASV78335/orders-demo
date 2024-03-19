<?php

namespace App\Unit\Domain\Exception;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class UnitAccessDeniedException extends AccessDeniedException
{
    public function __construct()
    {
        parent::__construct('Access to unit is denied');
    }
}