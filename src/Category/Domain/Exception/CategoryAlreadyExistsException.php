<?php

namespace App\Category\Domain\Exception;

use App\Shared\Domain\Exception\BusinessLogicException;

class CategoryAlreadyExistsException extends BusinessLogicException
{
    public function __construct(string $uuid)
    {
        parent::__construct('Category already exists. Maybe it`s marked as deleted: ' . $uuid);
    }

}