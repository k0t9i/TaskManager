<?php

declare(strict_types=1);

namespace App\Shared\Domain\ValueObject\Requests;

use App\Shared\Domain\Exception\LogicException;
use App\Shared\Domain\ValueObject\Status;

abstract class RequestStatus extends Status
{
    public const STATUS_PENDING = 0;
    public const STATUS_CONFIRMED = 1;
    public const STATUS_REJECTED = 2;

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
        if (self::STATUS_PENDING === $status) {
            return new PendingRequestStatus();
        }
        if (self::STATUS_CONFIRMED === $status) {
            return new ConfirmedRequestStatus();
        }
        if (self::STATUS_REJECTED === $status) {
            return new RejectedRequestStatus();
        }

        throw new LogicException(sprintf('Invalid project request status "%s"', gettype($status)));
    }

    public function isPending(): bool
    {
        return self::STATUS_PENDING === $this->getScalar();
    }

    public function isConfirmed(): bool
    {
        return self::STATUS_CONFIRMED === $this->getScalar();
    }
}
