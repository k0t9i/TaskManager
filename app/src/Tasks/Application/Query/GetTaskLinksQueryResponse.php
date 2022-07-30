<?php

declare(strict_types=1);

namespace App\Tasks\Application\Query;

use App\Shared\Application\Bus\Query\QueryResponseInterface;
use App\Shared\Application\DTO\PaginationItemsDTO;

final class GetTaskLinksQueryResponse implements QueryResponseInterface
{
    private readonly PaginationItemsDTO $pagination;

    public function __construct(PaginationItemsDTO $pagination)
    {
        $this->pagination = $pagination;
    }

    public function getPagination(): PaginationItemsDTO
    {
        return $this->pagination;
    }
}
