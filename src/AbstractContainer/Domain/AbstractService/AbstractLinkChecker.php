<?php

namespace App\AbstractContainer\Domain\AbstractService;

use App\AbstractContainer\Domain\Entity;
use App\Shared\Application\EntityProvider;

abstract class AbstractLinkChecker
{
    public function __construct(
        private readonly EntityProvider $entityProvider
    )
    {
    }

    abstract public function check(Entity $entity): bool;

    public function checkProducts(string $field, $entity): int
    {
        $usedProducts = $this->entityProvider
            ->getNotDeletedEntitiesByField('App\Product\Domain\Product', $field, $entity);
        return $usedProducts ? count($usedProducts) : 0;
    }

    public function checkPriceListEntries(string $field, $entity): int
    {
        $usedPriceListEntries = $this->entityProvider
            ->getNotDeletedEntitiesByField('App\Entity\PriceListEntry', $field, $entity);
        return $usedPriceListEntries ? count($usedPriceListEntries) : 0;
    }


    public function checkOrderEntries(string $field, $entity): int
    {
        $usedRecordEntries = $this->entityProvider
            ->getNotDeletedEntitiesByField('App\Entity\RecordEntry', $field, $entity);
        return $usedRecordEntries ? count($usedRecordEntries) : 0;
    }

    public function checkContragents(string $field, $entity): int
    {
        $usedContragents = $this->entityProvider
            ->getNotDeletedEntitiesByField('App\Entity\Contragent', $field, $entity);
        return $usedContragents ? count($usedContragents) : 0;
    }

    public function checkShops(string $field, $entity): int
    {
        $usedShops = $this->entityProvider
            ->getNotDeletedEntitiesByField('App\Entity\Shop', $field, $entity);
        return $usedShops ? count($usedShops) : 0;
    }
}
