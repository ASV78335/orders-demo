<?php

namespace App\Category\Domain\Exception;

use App\Shared\Domain\Exception\BusinessLogicException;

class ParentCategoryNotFoundException extends BusinessLogicException
{
    public function __construct()
    {
        parent::__construct('Parent category not found');
    }
}