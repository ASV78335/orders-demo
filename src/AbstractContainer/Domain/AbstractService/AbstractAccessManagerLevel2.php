<?php

namespace App\AbstractContainer\Domain\AbstractService;

use App\Entity\Person;
use App\Exception\Contragent\ContragentNotFoundException;
use App\Shared\Application\EntityProvider;

abstract class AbstractAccessManagerLevel2 extends AbstractAccessManager
{
    public function __construct(
        private readonly EntityProvider $provider
    )
    {
        parent::__construct($this->provider);
    }

    public function canCreate($person): bool
    {
        if (!$person instanceof Person) return false;

        $status = $person->getStatus();
        if ($status === $_ENV['ADMIN_STATUS']) return true;

        $currentContragent = $person->getCurrentContragent();
        if (null === $currentContragent) throw new ContragentNotFoundException();

        $managedContragents = $this->provider->getNotDeletedEntitiesByField('App\Entity\Contragent', 'basePerson', $person);
        if (!in_array($currentContragent, $managedContragents)) return false;

        return true;
    }
}
