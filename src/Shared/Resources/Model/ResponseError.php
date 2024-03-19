<?php

namespace App\Shared\Resources\Model;

use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

class ResponseError
{
    public function __construct(private string $message, private mixed $details = null)
    {
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    #[OA\Property(type: 'object', nullable: true, oneOf: [
        new OA\Schema(ref: new Model(type: ErrorDebugDetails::class)),
        new OA\Schema(ref: new Model(type: ErrorValidationList::class))]
    )]
    public function getDetails(): mixed
    {
        return $this->details;
    }
}