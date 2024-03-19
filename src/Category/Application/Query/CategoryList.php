<?php

namespace App\Category\Application\Query;

class CategoryList
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
}
