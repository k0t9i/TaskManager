<?php
declare(strict_types=1);

namespace App\Tasks\Application\Query;

use App\Shared\Application\Bus\Query\QueryResponseInterface;
use App\Tasks\Domain\DTO\TaskListResponseDTO;

final class GetProjectTasksQueryResponse implements QueryResponseInterface
{
    /**
     * @var TaskListResponseDTO[]
     */
    private readonly array $tasks;

    public function __construct(TaskListResponseDTO... $tasks)
    {
        $this->tasks = $tasks;
    }

    /**
     * @return TaskListResponseDTO[]
     */
    public function getTasks(): array
    {
        return $this->tasks;
    }
}
