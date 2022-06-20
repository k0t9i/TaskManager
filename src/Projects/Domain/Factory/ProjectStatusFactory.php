<?php
declare(strict_types=1);

namespace App\Projects\Domain\Factory;

use App\Projects\Domain\ValueObject\ActiveProjectStatus;
use App\Projects\Domain\ValueObject\ClosedProjectStatus;
use App\Projects\Domain\ValueObject\ProjectStatus;
use InvalidArgumentException;

class ProjectStatusFactory
{
    private const STATUS_CLOSED = 0;
    private const STATUS_ACTIVE = 1;

    public static function scalarFromObject(mixed $status): int
    {
        if ($status instanceof ClosedProjectStatus) {
            return self::STATUS_CLOSED;
        }
        if ($status instanceof ActiveProjectStatus) {
            return self::STATUS_ACTIVE;
        }

        throw new InvalidArgumentException(sprintf('Invalid project status %s', gettype($status)));
    }

    public static function objectFromScalar(int $status): ProjectStatus
    {
        if ($status === self::STATUS_CLOSED) {
            return new ClosedProjectStatus();
        }
        if ($status === self::STATUS_ACTIVE) {
            return new ActiveProjectStatus();
        }

        throw new InvalidArgumentException(sprintf('Invalid project status %s', gettype($status)));
    }
}
