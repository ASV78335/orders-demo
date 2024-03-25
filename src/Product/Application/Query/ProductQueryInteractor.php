<?php

namespace App\Product\Application\Query;

use App\AbstractContainer\Application\Query\AbstractQueryInteractor;
use App\Product\Application\ProductEntityProvider;
use App\Product\Domain\Exception\ProductAccessDeniedException;
use App\Product\Domain\Service\AccessManager;
use App\Shared\Application\Query\QueryInterface;

class ProductQueryInteractor extends AbstractQueryInteractor implements QueryInterface
{
    public function __construct(
        private readonly AccessManager         $accessManager,
        private readonly ProductEntityProvider $productEntityProvider
    )
    {
        $this->accessDeniedException = new ProductAccessDeniedException();
        $this->DTOList = new ProductList([]);

        parent::__construct(
            $this->accessManager,
            $this->productEntityProvider,
            $this->accessDeniedException,
            $this->DTOList
        );
    }
}
