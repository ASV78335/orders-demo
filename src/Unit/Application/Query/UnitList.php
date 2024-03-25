<?php

namespace App\Unit\Application\Query;

use App\Shared\Application\Query\DTOListInterface;

class UnitList implements DTOListInterface
{
    private array $items;

    /**
     * @param UnitItem[] $items
     */
    public function __construct(array $items)
    {
        $this->items = $items;
    }

    /**
     * @return UnitItem[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @param UnitItem[] $items
     */
    public function setItems(array $items): void
    {
        $this->items = $items;
    }
}
