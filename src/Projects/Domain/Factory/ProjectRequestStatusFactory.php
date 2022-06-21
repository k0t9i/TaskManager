<?php
declare(strict_types=1);

namespace App\Projects\Domain\Factory;

use App\Projects\Domain\ValueObject\ConfirmedProjectRequestStatus;
use App\Projects\Domain\ValueObject\PendingProjectRequestStatus;
use App\Projects\Domain\ValueObject\ProjectRequestStatus;
use App\Projects\Domain\ValueObject\RejectedProjectRequestStatus;
use InvalidArgumentException;

class ProjectRequestStatusFactory
{
    private const STATUS_PENDING = 0;
    private const STATUS_CONFIRMED = 1;
    private const STATUS_REJECTED = 1;

    public static function scalarFromObject(mixed $status): int
    {
        if ($status instanceof PendingProjectRequestStatus) {
            return self::STATUS_PENDING;
        }
        if ($status instanceof ConfirmedProjectRequestStatus) {
            return self::STATUS_CONFIRMED;
        }
        if ($status instanceof RejectedProjectRequestStatus) {
            return self::STATUS_REJECTED;
        }

        throw new InvalidArgumentException(sprintf('Invalid project request status %s', gettype($status)));
    }

    public static function objectFromScalar(int $status): ProjectRequestStatus
    {
        if ($status === self::STATUS_PENDING) {
            return new PendingProjectRequestStatus();
        }
        if ($status === self::STATUS_CONFIRMED) {
            return new ConfirmedProjectRequestStatus();
        }
        if ($status === self::STATUS_REJECTED) {
            return new RejectedProjectRequestStatus();
        }

        throw new InvalidArgumentException(sprintf('Invalid project request status %s', gettype($status)));
    }
}
