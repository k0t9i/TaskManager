<?php
declare(strict_types=1);

namespace App\Tasks\Application\Query;

use App\Shared\Application\Bus\Query\QueryResponseInterface;
use App\Shared\Application\DTO\PaginationDTO;
use App\Tasks\Domain\Entity\TaskListProjection;

final class GetProjectTasksQueryResponse implements QueryResponseInterface
{
    private readonly PaginationDTO $pagination;
    /**
     * @var TaskListProjection[]
     */
    private readonly array $items;

    public function __construct(PaginationDTO $pagination, TaskListProjection... $items)
    {
        $this->pagination = $pagination;
        $this->items = $items;
    }

    public function getPagination(): PaginationDTO
    {
        return $this->pagination;
    }

    /**
     * @return TaskListProjection[]
     */
    public function getItems(): array
    {
        return $this->items;
    }
}
