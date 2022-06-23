<?php
declare(strict_types=1);

namespace App\ProjectRequests\Domain\Entity;

use App\ProjectRequests\Domain\ValueObject\PendingRequestStatus;
use App\ProjectRequests\Domain\ValueObject\RequestChangeDate;
use App\ProjectRequests\Domain\ValueObject\RequestId;
use App\ProjectRequests\Domain\ValueObject\RequestStatus;
use App\Shared\Domain\Aggregate\AggregateRoot;
use App\Users\Domain\ValueObject\UserId;

final class Request extends AggregateRoot
{
    public function __construct(
        private RequestId $id,
        private UserId $userId,
        private RequestStatus $status,
        private RequestChangeDate $changeDate
    ) {
    }

    public static function create(RequestId $id, UserId $requestUserId): self
    {
        $status = new PendingRequestStatus();
        $changeDate = new RequestChangeDate(date('c'));
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

    public function getChangeDate(): RequestChangeDate
    {
        return $this->changeDate;
    }
}
