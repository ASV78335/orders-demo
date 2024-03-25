<?php

namespace App\Shared\Application\Query;

interface DTOListInterface
{
    public function getItems(): array;
    public function setItems(array $items): void;
}
