<?php

namespace App\Product\Domain\Exception;

use App\Shared\Domain\Exception\BusinessLogicException;

class ProductIsUsedException extends BusinessLogicException
{
    public function __construct(int $count)
    {
        parent::__construct(sprintf('Product is used in %d entries', $count));
    }
}