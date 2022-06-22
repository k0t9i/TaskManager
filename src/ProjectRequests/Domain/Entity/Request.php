<?php
declare(strict_types=1);

namespace App\ProjectRequests\Domain\Entity;

use App\ProjectRequests\Domain\ValueObject\RequestChangeDate;
use App\ProjectRequests\Domain\ValueObject\RequestId;
use App\ProjectRequests\Domain\ValueObject\RequestStatus;
use App\Shared\Domain\Aggregate\AggregateRoot;

final class Request extends AggregateRoot
{
    public function __construct(
        private RequestId $id,
        private RequestUser $user,
        private RequestStatus $status,
        private RequestChangeDate $changeDate
    ) {
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

    public function getUser(): RequestUser
    {
        return $this->user;
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
