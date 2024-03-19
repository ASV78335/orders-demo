<?php

namespace App\Unit\Domain\Exception;

use App\Shared\Domain\Exception\BusinessLogicException;

class UnitNotFoundException extends BusinessLogicException
{
    public function __construct()
    {
        parent::__construct('Unit not found');
    }
}
