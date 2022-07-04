<?php
declare(strict_types=1);

namespace App\Tasks\Application\Query;

use App\Shared\Domain\Bus\Query\QueryResponseInterface;

final class GetAllProjectTasksQueryResponse implements QueryResponseInterface
{
    /**
     * @var TaskResponse[]
     */
    private readonly array $tasks;

    public function __construct(TaskResponse... $tasks)
    {
        $this->tasks = $tasks;
    }

    /**
     * @return TaskResponse[]
     */
    public function getTasks(): array
    {
        return $this->tasks;
    }
}
