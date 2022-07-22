<?php
declare(strict_types=1);

namespace App\Requests\Application\Query;

use App\Requests\Domain\Entity\RequestListProjection;
use App\Shared\Application\Bus\Query\QueryResponseInterface;
use App\Shared\Application\DTO\PaginationDTO;

final class GetProjectRequestsQueryResponse implements QueryResponseInterface
{
    private readonly PaginationDTO $pagination;
    /**
     * @var RequestListProjection[]
     */
    private readonly array $items;

    public function __construct(PaginationDTO $pagination, RequestListProjection... $items)
    {
        $this->pagination = $pagination;
        $this->items = $items;
    }

    public function getPagination(): PaginationDTO
    {
        return $this->pagination;
    }

    /**
     * @return RequestListProjection[]
     */
    public function getItems(): array
    {
        return $this->items;
    }
}
