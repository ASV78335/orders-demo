<?php

namespace App\Unit\Application\Query;

class UnitList
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
}
