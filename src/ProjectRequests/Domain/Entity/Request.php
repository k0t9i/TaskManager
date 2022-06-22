<?php
declare(strict_types=1);

namespace App\ProjectRequests\Domain\Entity;

use App\ProjectRequests\Domain\Event\RequestStatusWasChangedEvent;
use App\ProjectRequests\Domain\Event\RequestWasCreatedEvent;
use App\ProjectRequests\Domain\Factory\RequestStatusFactory;
use App\ProjectRequests\Domain\ValueObject\ConfirmedRequestStatus;
use App\ProjectRequests\Domain\ValueObject\PendingRequestStatus;
use App\ProjectRequests\Domain\ValueObject\RequestChangeDate;
use App\ProjectRequests\Domain\ValueObject\RequestId;
use App\ProjectRequests\Domain\ValueObject\RequestStatus;
use App\ProjectRequests\Domain\ValueObject\RequestUser;
use App\Projects\Domain\Event\ProjectParticipantWasAddedEvent;
use App\Shared\Domain\Aggregate\AggregateRoot;
use App\Users\Domain\ValueObject\UserId;

final class Request extends AggregateRoot
{
    public function __construct(
        public readonly RequestId $id,
        public readonly RequestProject $project,
        public readonly RequestUser $user,
        public readonly RequestStatus $status,
        public readonly RequestChangeDate $changeDate
    ) {
    }

    public static function create(RequestId $id, RequestProject $project, RequestUser $requestUser): self
    {
        $status = new PendingRequestStatus();
        $changeDate = new RequestChangeDate(date('c'));
        $request = new self($id, $project, $requestUser, $status, $changeDate);
        $project->addRequest($request);

        $request->registerEvent(new RequestWasCreatedEvent(
            $request->id->value,
            $request->project->getId()->value,
            $request->user->userId->value,
        ));

        return $request;
    }

    public function changeStatus(
        RequestStatus $status,
        UserId        $currentUserId
    ): void {
        $this->project->ensureCanPerformOwnerOperations($currentUserId);

        $this->getStatus()->ensureCanBeChangedTo($status);
        $this->setStatus($status);
        if ($status instanceof ConfirmedRequestStatus) {
            $this->project->addParticipantFromRequest($this);
            $this->registerEvent(new ProjectParticipantWasAddedEvent(
                $this->project->getId()->value,
                $this->getUser()->userId->value
            ));
        }

        $this->registerEvent(new RequestStatusWasChangedEvent(
            $this->getId()->value,
            RequestStatusFactory::scalarFromObject($status)
        ));
    }

    public function getId(): RequestId
    {
        return $this->id;
    }

    public function getUser(): RequestUser
    {
        return $this->user;
    }

    private function getStatus(): RequestStatus
    {
        return $this->status;
    }

    private function setStatus(RequestStatus $status): void
    {
        $this->status = $status;
    }
}
