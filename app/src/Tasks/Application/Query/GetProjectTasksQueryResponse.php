<?php
declare(strict_types=1);

namespace App\Tasks\Application\Query;

use App\Shared\Application\Bus\Query\QueryResponseInterface;
use App\Tasks\Domain\Entity\TaskListProjection;

final class GetProjectTasksQueryResponse implements QueryResponseInterface
{
    /**
     * @var TaskListProjection[]
     */
    private readonly array $tasks;

    public function __construct(TaskListProjection... $tasks)
    {
        $this->tasks = $tasks;
    }

    /**
     * @return TaskListProjection[]
     */
    public function getTasks(): array
    {
        return $this->tasks;
    }
}
