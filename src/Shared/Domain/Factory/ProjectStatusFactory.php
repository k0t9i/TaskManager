<?php
declare(strict_types=1);

namespace App\Shared\Domain\Factory;

use App\Shared\Domain\Exception\LogicException;
use App\Shared\Domain\ValueObject\ActiveProjectStatus;
use App\Shared\Domain\ValueObject\ClosedProjectStatus;
use App\Shared\Domain\ValueObject\ProjectStatus;

final class ProjectStatusFactory
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

        throw new LogicException(sprintf('Invalid type "%s" of project status', gettype($status)));
    }

    public static function objectFromScalar(int $status): ProjectStatus
    {
        if ($status === self::STATUS_CLOSED) {
            return new ClosedProjectStatus();
        }
        if ($status === self::STATUS_ACTIVE) {
            return new ActiveProjectStatus();
        }

        throw new LogicException(sprintf('Invalid project status "%s"', gettype($status)));
    }
}
