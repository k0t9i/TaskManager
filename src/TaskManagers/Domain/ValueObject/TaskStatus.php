<?php
declare(strict_types=1);

namespace App\TaskManagers\Domain\ValueObject;

use App\Shared\Domain\ValueObject\Status;
use App\TaskManagers\Domain\Factory\TaskStatusFactory;

abstract class TaskStatus extends Status
{
    public function getScalar(): int
    {
        return TaskStatusFactory::scalarFromObject($this);
    }
}