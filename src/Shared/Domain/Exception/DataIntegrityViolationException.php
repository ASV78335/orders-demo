<?php

namespace App\Shared\Domain\Exception;

class DataIntegrityViolationException extends BusinessLogicException
{
    public function __construct($description)
    {
        parent::__construct('Data integrity violation: ' . $description);
    }

}
