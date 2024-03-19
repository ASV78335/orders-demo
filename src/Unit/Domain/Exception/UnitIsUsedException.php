<?php

namespace App\Unit\Domain\Exception;

use App\Shared\Domain\Exception\BusinessLogicException;

class UnitIsUsedException extends BusinessLogicException
{
    public function __construct(int $count)
    {
        parent::__construct(sprintf('Unit is used %d times', $count));
    }

}
