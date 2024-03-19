<?php

namespace App\Category\Domain\Exception;

use App\Shared\Domain\Exception\BusinessLogicException;

class CategoryIsUsedException extends BusinessLogicException
{
    public function __construct(int $count)
    {
        parent::__construct(sprintf('Category is used in %d products', $count));
    }

}