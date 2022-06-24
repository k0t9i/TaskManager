<?php
declare(strict_types=1);

namespace App\ProjectRequests\Domain\Entity;

use App\ProjectRequests\Domain\ValueObject\ConfirmedRequestStatus;
use App\ProjectRequests\Domain\ValueObject\PendingRequestStatus;
use App\ProjectRequests\Domain\ValueObject\RequestId;
use App\ProjectRequests\Domain\ValueObject\RequestStatus;
use App\Shared\Domain\Collection\Hashable;
use App\Shared\Domain\ValueObject\DateTime;
use App\Shared\Domain\ValueObject\UserId;

final class Request implements Hashable
{
    public function __construct(
        private RequestId $id,
        private UserId $userId,
        private RequestStatus $status,
        private DateTime $changeDate
    ) {
    }

    public static function create(RequestId $id, UserId $requestUserId): self
    {
        $status = new PendingRequestStatus();
        $changeDate = new DateTime(date('c'));
        return new Request($id, $requestUserId, $status, $changeDate);
    }

    public function changeStatus(
        RequestStatus $status
    ): void {
        $this->getStatus()->ensureCanBeChangedTo($status);
        $this->status = $status;
    }

    public function getId(): RequestId
    {
        return $this->id;
    }

    public function getUserId(): UserId
    {
        return $this->userId;
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

    public function isConfirmed(): bool
    {
        return $this->status instanceof ConfirmedRequestStatus;
    }

    public function isPending(): bool
    {
        return $this->status instanceof PendingRequestStatus;
    }

    public function isNonRejected(): bool
    {
        return $this->isConfirmed() || $this->isPending();
    }
}
