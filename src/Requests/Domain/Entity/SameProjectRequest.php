<?php
declare(strict_types=1);

namespace App\Requests\Domain\Entity;

use App\Requests\Domain\ValueObject\ConfirmedRequestStatus;
use App\Requests\Domain\ValueObject\PendingRequestStatus;
use App\Requests\Domain\ValueObject\RequestId;
use App\Requests\Domain\ValueObject\RequestStatus;
use App\Shared\Domain\Collection\Hashable;
use App\Shared\Domain\ValueObject\UserId;

final class SameProjectRequest implements Hashable
{
    public function __construct(
        private RequestId     $id,
        private UserId        $userId,
        private RequestStatus $status
    ) {
    }

    /**
     * @return RequestId
     */
    public function getId(): RequestId
    {
        return $this->id;
    }

    /**
     * @return UserId
     */
    public function getUserId(): UserId
    {
        return $this->userId;
    }

    /**
     * @return RequestStatus
     */
    public function getStatus(): RequestStatus
    {
        return $this->status;
    }

    public function getHash(): string
    {
        return $this->id->getHash();
    }

    public function isNonRejected(): bool
    {
        return $this->isConfirmed() || $this->isPending();
    }

    private function isConfirmed(): bool
    {
        return $this->status instanceof ConfirmedRequestStatus;
    }

    private function isPending(): bool
    {
        return $this->status instanceof PendingRequestStatus;
    }
}
