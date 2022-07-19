<?php
declare(strict_types=1);

namespace App\Projects\Application\Query;

use App\Projects\Domain\DTO\ProjectListResponseDTO;
use App\Shared\Application\DTO\PaginationDTO;
use App\Shared\Domain\Bus\Query\QueryResponseInterface;

final class GetAllOwnProjectsQueryResponse implements QueryResponseInterface
{
    private readonly PaginationDTO $pagination;
    /**
     * @var ProjectListResponseDTO[]
     */
    private readonly array $items;

    public function __construct(PaginationDTO $pagination, ProjectListResponseDTO... $items)
    {
        $this->pagination = $pagination;
        $this->items = $items;
    }

    public function getPagination(): PaginationDTO
    {
        return $this->pagination;
    }

    /**
     * @return ProjectListResponseDTO[]
     */
    public function getItems(): array
    {
        return $this->items;
    }
}
