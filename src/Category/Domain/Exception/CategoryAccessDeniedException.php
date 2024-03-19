<?php

namespace App\Category\Domain\Exception;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class CategoryAccessDeniedException extends AccessDeniedException
{
    public function __construct()
    {
        parent::__construct('Access to category is denied');
    }
}
