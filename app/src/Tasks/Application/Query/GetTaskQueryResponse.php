<?php
declare(strict_types=1);

namespace App\Tasks\Application\Query;

use App\Shared\Domain\Bus\Query\QueryResponseInterface;
use App\Tasks\Domain\DTO\TaskResponseDTO;

final class GetTaskQueryResponse implements QueryResponseInterface
{
    private readonly TaskResponseDTO $task;

    public function __construct(TaskResponseDTO $task)
    {
        $this->task = $task;
    }

    public function getTask(): TaskResponseDTO
    {
        return $this->task;
    }
}
