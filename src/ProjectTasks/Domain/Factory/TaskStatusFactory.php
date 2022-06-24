<?php
declare(strict_types=1);

namespace App\ProjectTasks\Domain\Factory;

use App\ProjectTasks\Domain\ValueObject\ActiveTaskStatus;
use App\ProjectTasks\Domain\ValueObject\ClosedTaskStatus;
use App\ProjectTasks\Domain\ValueObject\TaskStatus;
use InvalidArgumentException;

final class TaskStatusFactory
{
    private const STATUS_CLOSED = 0;
    private const STATUS_ACTIVE = 1;

    public static function scalarFromObject(mixed $status): int
    {
        if ($status instanceof ClosedTaskStatus) {
            return self::STATUS_CLOSED;
        }
        if ($status instanceof ActiveTaskStatus) {
            return self::STATUS_ACTIVE;
        }

        throw new InvalidArgumentException(sprintf('Invalid task status %s', gettype($status)));
    }

    public static function objectFromScalar(int $status): TaskStatus
    {
        if ($status === self::STATUS_CLOSED) {
            return new ClosedTaskStatus();
        }
        if ($status === self::STATUS_ACTIVE) {
            return new ActiveTaskStatus();
        }

        throw new InvalidArgumentException(sprintf('Invalid task status %s', gettype($status)));
    }
}
