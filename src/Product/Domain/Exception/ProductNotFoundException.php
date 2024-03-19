<?php

namespace App\Product\Domain\Exception;

use App\Shared\Domain\Exception\BusinessLogicException;

class ProductNotFoundException extends BusinessLogicException
{
    public function __construct()
    {
        parent::__construct('Product not found');
    }
}