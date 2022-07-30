<?php

declare(strict_types=1);

namespace App\Tasks\Domain\ValueObject;

use App\Shared\Domain\Exception\LogicException;
use App\Shared\Domain\ValueObject\Status;

abstract class TaskStatus extends Status
{
    private const STATUS_CLOSED = 0;
    private const STATUS_ACTIVE = 1;

    public function getScalar(): int
    {
        if ($this instanceof ClosedTaskStatus) {
            return self::STATUS_CLOSED;
        }
        if ($this instanceof ActiveTaskStatus) {
            return self::STATUS_ACTIVE;
        }

        throw new LogicException(sprintf('Invalid type "%s" of task status', gettype($this)));
    }

    public static function createFromScalar(int $status): static
    {
        if (self::STATUS_CLOSED === $status) {
            return new ClosedTaskStatus();
        }
        if (self::STATUS_ACTIVE === $status) {
            return new ActiveTaskStatus();
        }

        throw new LogicException(sprintf('Invalid task status "%s"', gettype($status)));
    }
}
