<?php
declare(strict_types=1);

namespace App\ProjectTasks\Domain\ValueObject;

use App\ProjectTasks\Domain\Factory\TaskStatusFactory;
use App\Shared\Domain\ValueObject\Status;

abstract class TaskStatus extends Status
{
    public function getScalar(): int
    {
        return TaskStatusFactory::scalarFromObject($this);
    }
}