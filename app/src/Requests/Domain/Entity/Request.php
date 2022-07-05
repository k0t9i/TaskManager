<?php
declare(strict_types=1);

namespace App\Requests\Domain\Entity;

use App\Requests\Domain\ValueObject\ConfirmedRequestStatus;
use App\Requests\Domain\ValueObject\PendingRequestStatus;
use App\Requests\Domain\ValueObject\RequestId;
use App\Requests\Domain\ValueObject\RequestStatus;
use App\Shared\Domain\Collection\Hashable;
use App\Shared\Domain\ValueObject\DateTime;
use App\Shared\Domain\ValueObject\Owner;

final class Request implements Hashable
{
    public function __construct(
        private RequestId $id,
        private Owner $user,
        private RequestStatus $status,
        private DateTime $changeDate
    ) {
    }

    public function changeStatus(RequestStatus $status): void
    {
        $this->status->ensureCanBeChangedTo($status);
        $this->status = $status;
    }

    public function getId(): RequestId
    {
        return $this->id;
    }

    public function getUser(): Owner
    {
        return $this->user;
    }

    public function getStatus(): RequestStatus
    {
        return $this->status;
    }

    public function getChangeDate(): DateTime
    {
        return $this->changeDate;
    }

    public function getHash(): string
    {
        return $this->getId()->getHash();
    }

    /**
     * @param self $other
     * @return bool
     */
    public function isEqual(object $other): bool
    {
        if (get_class($this) !== get_class($other)) {
            return false;
        }
        return $this->id->isEqual($other->id) &&
            $this->user->isEqual($other->user) &&
            $this->status->isEqual($other->status) &&
            $this->changeDate->isEqual($other->changeDate);
    }

    public function isConfirmed(): bool
    {
        return $this->status instanceof ConfirmedRequestStatus;
    }

    public function isPending(): bool
    {
        return $this->status instanceof PendingRequestStatus;
    }
}
