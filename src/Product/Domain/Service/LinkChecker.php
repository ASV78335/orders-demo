<?php

namespace App\Product\Domain\Service;

use App\AbstractContainer\Domain\AbstractService\AbstractLinkChecker;
use App\AbstractContainer\Domain\Entity;
use App\Product\Domain\Exception\ProductIsUsedException;

class LinkChecker extends AbstractLinkChecker
{

    public function check(Entity $entity): bool
    {
        $countOfUsedPriceListEntries = $this->checkPriceListEntries('product', $entity);
        $countOfUsedRecordEntries = $this->checkOrderEntries('product', $entity);

        $countOfUses = $countOfUsedPriceListEntries + $countOfUsedRecordEntries;
        if ($countOfUses > 0) throw new ProductIsUsedException($countOfUses);

        return true;
    }
}
