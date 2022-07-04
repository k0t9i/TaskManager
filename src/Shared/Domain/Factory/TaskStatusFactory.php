<?php
declare(strict_types=1);

namespace App\Shared\Domain\Factory;

use App\Shared\Domain\Exception\LogicException;
use App\Shared\Domain\ValueObject\ActiveTaskStatus;
use App\Shared\Domain\ValueObject\ClosedTaskStatus;
use App\Shared\Domain\ValueObject\TaskStatus;

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

        throw new LogicException(sprintf('Invalid type "%s" of task status', gettype($status)));
    }

    public static function objectFromScalar(int $status): TaskStatus
    {
        if ($status === self::STATUS_CLOSED) {
            return new ClosedTaskStatus();
        }
        if ($status === self::STATUS_ACTIVE) {
            return new ActiveTaskStatus();
        }

        throw new LogicException(sprintf('Invalid task status "%s"', gettype($status)));
    }
}
