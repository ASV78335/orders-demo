<?php

namespace App\Product\Domain\Exception;

use App\Shared\Domain\Exception\BusinessLogicException;

class ProductAlreadyExistsException extends BusinessLogicException
{
    public function __construct()
    {
        parent::__construct('Product already exists. Maybe it`s marked as deleted');
    }
}
