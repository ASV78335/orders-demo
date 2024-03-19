<?php

namespace App\Product\Application\Query;

class ProductList
{
    /**
     * @var ProductItem[]
     */
    private array $items;

    /**
     * @param ProductItem[] $items
     */
    public function __construct(array $items)
    {
        $this->items = $items;
    }

    /**
     * @return ProductItem[]
     */
    public function getItems(): array
    {
        return $this->items;
    }
}
