<?php

namespace App\Unit\Domain\Service;

use App\AbstractContainer\Domain\AbstractService\AbstractLinkChecker;
use App\AbstractContainer\Domain\Entity;
use App\Unit\Domain\Exception\UnitIsUsedException;

class LinkChecker extends AbstractLinkChecker
{
    public function check(Entity $entity): bool
    {
        $countNotDeletedRelatedProducts = $this->checkProducts('baseUnit', $entity);
        $countOfUsedPriceListEntries = $this->checkPriceListEntries('unit', $entity);
        $countOfUsedRecordEntries = $this->checkOrderEntries('unit', $entity);

        $countOfUses = $countNotDeletedRelatedProducts + $countOfUsedPriceListEntries + $countOfUsedRecordEntries;
        if ($countOfUses > 0) throw new UnitIsUsedException($countOfUses);

        return true;
    }
}
