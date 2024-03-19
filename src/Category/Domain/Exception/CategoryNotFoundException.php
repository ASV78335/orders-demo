<?php

namespace App\Category\Domain\Exception;

use App\Shared\Domain\Exception\BusinessLogicException;

class CategoryNotFoundException extends BusinessLogicException
{
    public function __construct()
    {
        parent::__construct('Category not found');
    }
}