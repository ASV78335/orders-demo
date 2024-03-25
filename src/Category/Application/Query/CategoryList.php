<?php

namespace App\Category\Application\Query;

use App\Shared\Application\Query\DTOListInterface;

class CategoryList implements DTOListInterface
{
    private array $items;

    /**
     * @param CategoryItem[] $items
     */
    public function __construct(array $items)
    {
        $this->items = $items;
    }

    /**
     * @return CategoryItem[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @param CategoryItem[] $items
     */
    public function setItems(array $items): void
    {
        $this->items = $items;
    }
}
