<?php
declare(strict_types=1);

namespace App\Requests\Domain\Entity;

use App\Requests\Domain\Event\ProjectParticipantWasAddedEvent;
use App\Requests\Domain\Event\RequestStatusWasChangedEvent;
use App\Requests\Domain\Event\RequestWasCreatedEvent;
use App\Requests\Domain\ValueObject\ConfirmedRequestStatus;
use App\Requests\Domain\ValueObject\PendingRequestStatus;
use App\Requests\Domain\ValueObject\RequestId;
use App\Requests\Domain\ValueObject\RequestStatus;
use App\Shared\Domain\Aggregate\AggregateRoot;
use App\Shared\Domain\Collection\Hashable;
use App\Shared\Domain\ValueObject\DateTime;
use App\Shared\Domain\ValueObject\ProjectId;
use App\Shared\Domain\ValueObject\UserId;

final class Request extends AggregateRoot implements Hashable
{
    public function __construct(
        private RequestId $id,
        private ProjectId $projectId,
        private UserId $userId,
        private RequestStatus $status,
        private DateTime $changeDate,
        private RequestProject $requestProject
    ) {
    }

    public static function create(
        RequestId $id,
        ProjectId $projectId,
        UserId $requestUserId,
        RequestProject $requestProject
    ): Request {
        $status = new PendingRequestStatus();
        $changeDate = new DateTime();
        $request = new self($id, $projectId, $requestUserId, $status, $changeDate, $requestProject);

        $request->requestProject->ensureCanAddRequest($request->getUserId());

        $request->registerEvent(new RequestWasCreatedEvent(
            $request->id->value,
            $requestUserId->value,
        ));

        return $request;
    }

    public function changeStatus(
        RequestStatus $status,
        UserId $currentUserId
    ): void {
        $this->requestProject->ensureCanChangeRequest($currentUserId);
        $this->getStatus()->ensureCanBeChangedTo($status);

        $this->status = $status;

        if ($this->isConfirmed()) {
            $this->requestProject->addParticipantFromRequest($this->userId);
            $this->registerEvent(new ProjectParticipantWasAddedEvent(
                $this->id->value,
                $this->projectId->value,
                $this->userId->value
            ));
        }

        $this->registerEvent(new RequestStatusWasChangedEvent(
            $this->id->value,
            (string) $this->status->getScalar()
        ));
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

    private function isConfirmed(): bool
    {
        return $this->status instanceof ConfirmedRequestStatus;
    }
}
