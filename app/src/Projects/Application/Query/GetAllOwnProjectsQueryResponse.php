<?php
declare(strict_types=1);

namespace App\Projects\Application\Query;

use App\Projects\Domain\Entity\ProjectListProjection;
use App\Shared\Application\Bus\Query\QueryResponseInterface;
use App\Shared\Application\DTO\PaginationDTO;

final class GetAllOwnProjectsQueryResponse implements QueryResponseInterface
{
    private readonly PaginationDTO $pagination;
    /**
     * @var ProjectListProjection[]
     */
    private readonly array $items;

    public function __construct(PaginationDTO $pagination, ProjectListProjection... $items)
    {
        $this->pagination = $pagination;
        $this->items = $items;
    }

    public function getPagination(): PaginationDTO
    {
        return $this->pagination;
    }

    /**
     * @return ProjectListProjection[]
     */
    public function getItems(): array
    {
        return $this->items;
    }
}
