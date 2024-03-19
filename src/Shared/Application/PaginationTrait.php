<?php

namespace App\Shared\Application;

trait PaginationTrait
{
    private function getPaginationInfo(int $entitiesCount, int $entitiesForPage = 10, int $page = 1): array
    {
        if ($entitiesForPage === 0) $entitiesForPage = 10;
        $pageCount = ceil($entitiesCount / $entitiesForPage);

        if ($page < 1) $page = 1;
        if ($page > $pageCount) $page = $pageCount;

        $offset = $page * $entitiesForPage - $entitiesForPage;

        $prevPage = $page - 1;
        if ($prevPage === 0) $prevPage = null;

        $firstPage = 1;
        if ($page == 1) $firstPage = null;

        $nextPage = $page + 1;
        if ($page == $pageCount) $nextPage = null;

        $lastPage = (int) $pageCount;
        if ($page == $pageCount) $lastPage = null;

        return [
            'entitiesCount' => $entitiesCount,
            'entitiesForPage' => $entitiesForPage,
            'page' => $page,
            'offset' => $offset,
            'prevPage' => $prevPage,
            'firstPage' => $firstPage,
            'nextPage' => $nextPage,
            'lastPage' => $lastPage
        ];
    }
}
