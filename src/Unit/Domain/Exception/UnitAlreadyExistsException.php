<?php

namespace App\Unit\Domain\Exception;

use App\Shared\Domain\Exception\BusinessLogicException;

class UnitAlreadyExistsException extends BusinessLogicException
{
    public function __construct(string $uuid)
    {
        parent::__construct('Unit already exists. Maybe it`s marked as deleted: ' . $uuid);
    }

}