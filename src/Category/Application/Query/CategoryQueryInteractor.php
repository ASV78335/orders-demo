<?php

namespace App\Category\Application\Query;

use App\AbstractContainer\Application\Query\AbstractQueryInteractor;
use App\Category\Application\CategoryEntityProvider;
use App\Category\Domain\Exception\CategoryAccessDeniedException;
use App\Category\Domain\Service\AccessManager;
use App\Shared\Application\Query\QueryInterface;

class CategoryQueryInteractor extends AbstractQueryInteractor implements QueryInterface
{
    public function __construct(
        private readonly AccessManager          $accessManager,
        private readonly CategoryEntityProvider $categoryEntityProvider
    )
    {
        $this->accessDeniedException = new CategoryAccessDeniedException();
        $this->DTOList = new CategoryList([]);

        parent::__construct(
            $this->accessManager,
            $this->categoryEntityProvider,
            $this->accessDeniedException,
            $this->DTOList
        );
    }
}
