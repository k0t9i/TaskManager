<?php
declare(strict_types=1);

namespace App\ProjectTasks\Domain\Collection;

use App\ProjectTasks\Domain\Entity\Task;
use App\Shared\Domain\Collection\Collection;

class TaskCollection extends Collection
{
    protected function getType(): string
    {
        return Task::class;
    }
}
