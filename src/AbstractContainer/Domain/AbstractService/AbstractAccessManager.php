<?php

namespace App\AbstractContainer\Domain\AbstractService;

use App\AbstractContainer\Domain\Entity;
use App\Entity\Person;
use App\Shared\Application\EntityProvider;

abstract class AbstractAccessManager
{
    public function __construct(
        private readonly EntityProvider $entityProvider
    )
    {
    }

    public function canView($person, Entity $entity): bool
    {
        if (!$person instanceof Person) return false;

        return true;
    }

    protected function checkedInContragents(Person $person, string $field, $entity): bool
    {
        $currentContragent = $person->getCurrentContragent();

        $contragents = $this->entityProvider
            ->getNotDeletedEntitiesByField('App\Entity\Contragent', $field, $entity);
        if (!empty($contragents) && !in_array($currentContragent, $contragents)) return false;

        return true;
    }

    protected function checkedInShops(Person $person, string $field, $entity): bool
    {
        $currentContragent = $person->getCurrentContragent();

        $shops = $this->entityProvider->getNotDeletedEntitiesByField('App\Entity\Shop', $field, $entity);

        $relatedInShops = array_map(fn($value) => $value->getContragent(), $shops);
        if (!empty($relatedInShops) && !in_array($currentContragent, $relatedInShops)) return false;

        return true;
    }
}
