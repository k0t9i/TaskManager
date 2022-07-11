<?php
declare(strict_types=1);

namespace App\Shared\Domain\ValueObject\Requests;

use App\Shared\Domain\Exception\LogicException;
use App\Shared\Domain\ValueObject\Status;

abstract class RequestStatus extends Status
{
    private const STATUS_PENDING = 0;
    private const STATUS_CONFIRMED = 1;
    private const STATUS_REJECTED = 2;

    public function allowsModification(): bool
    {
        return true;
    }

    public function getScalar(): int
    {
        if ($this instanceof PendingRequestStatus) {
            return self::STATUS_PENDING;
        }
        if ($this instanceof ConfirmedRequestStatus) {
            return self::STATUS_CONFIRMED;
        }
        if ($this instanceof RejectedRequestStatus) {
            return self::STATUS_REJECTED;
        }

        throw new LogicException(sprintf('Invalid type "%s" of project request status', gettype($this)));
    }

    public static function createFromScalar(int $status): static
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

        throw new LogicException(sprintf('Invalid project request status "%s"', gettype($status)));
    }

    public function isPending(): bool
    {
        return $this->getScalar() === self::STATUS_PENDING;
    }

    public function isConfirmed(): bool
    {
        return $this->getScalar() === self::STATUS_CONFIRMED;
    }
}
