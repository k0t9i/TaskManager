<?php
declare(strict_types=1);

namespace App\Shared\Domain\ValueObject;

use App\Shared\Domain\Factory\TaskStatusFactory;

abstract class TaskStatus extends Status
{
    public function getScalar(): int
    {
        return TaskStatusFactory::scalarFromObject($this);
    }
}