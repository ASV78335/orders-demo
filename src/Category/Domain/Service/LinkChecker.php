<?php

namespace App\Category\Domain\Service;

use App\AbstractContainer\Domain\AbstractService\AbstractLinkChecker;
use App\AbstractContainer\Domain\Entity;
use App\Category\Domain\Exception\CategoryIsUsedException;

class LinkChecker extends AbstractLinkChecker
{
    public function check(Entity $entity): bool
    {
        $countNotDeletedRelatedProducts = $this->checkProducts('category', $entity);
        if ($countNotDeletedRelatedProducts > 0) throw new CategoryIsUsedException($countNotDeletedRelatedProducts);

        return true;
    }
}
