<?php
declare(strict_types=1);

namespace App\ProjectRequests\Domain\Factory;

use App\ProjectRequests\Domain\ValueObject\ConfirmedRequestStatus;
use App\ProjectRequests\Domain\ValueObject\PendingRequestStatus;
use App\ProjectRequests\Domain\ValueObject\RejectedRequestStatus;
use App\ProjectRequests\Domain\ValueObject\RequestStatus;
use InvalidArgumentException;

final class RequestStatusFactory
{
    private const STATUS_PENDING = 0;
    private const STATUS_CONFIRMED = 1;
    private const STATUS_REJECTED = 1;

    public static function scalarFromObject(mixed $status): int
    {
        if ($status instanceof PendingRequestStatus) {
            return self::STATUS_PENDING;
        }
        if ($status instanceof ConfirmedRequestStatus) {
            return self::STATUS_CONFIRMED;
        }
        if ($status instanceof RejectedRequestStatus) {
            return self::STATUS_REJECTED;
        }

        throw new InvalidArgumentException(sprintf('Invalid project request status %s', gettype($status)));
    }

    public static function objectFromScalar(int $status): RequestStatus
    {
        if ($status === self::STATUS_PENDING) {
            return new PendingRequestStatus();
        }
        if ($status === self::STATUS_CONFIRMED) {
            return new ConfirmedRequestStatus();
        }
        if ($status === self::STATUS_REJECTED) {
            return new RejectedRequestStatus();
        }

        throw new InvalidArgumentException(sprintf('Invalid project request status %s', gettype($status)));
    }
}
