<?php

namespace App\Shared\Application\Exception;

use RuntimeException;

class RequestParsingException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct(' Error while parsing request');
    }
}
