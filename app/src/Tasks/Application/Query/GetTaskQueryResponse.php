<?php

declare(strict_types=1);

namespace App\Tasks\Application\Query;

use App\Shared\Application\Bus\Query\QueryResponseInterface;
use App\Tasks\Domain\Entity\TaskProjection;

final class GetTaskQueryResponse implements QueryResponseInterface
{
    private readonly TaskProjection $task;

    public function __construct(TaskProjection $task)
    {
        $this->task = $task;
    }

    public function getTask(): TaskProjection
    {
        return $this->task;
    }
}
