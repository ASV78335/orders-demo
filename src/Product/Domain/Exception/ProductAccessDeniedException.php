<?php

namespace App\Product\Domain\Exception;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ProductAccessDeniedException extends AccessDeniedException
{
    public function __construct()
    {
        parent::__construct('Access to product is denied');
    }
}
