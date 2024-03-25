<?php

namespace App\Product\Application\Query;

use App\Shared\Application\Query\DTOListInterface;

class ProductList implements DTOListInterface
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

    /**
     * @param ProductItem[] $items
     */
    public function setItems(array $items): void
    {
        $this->items = $items;
    }
}
