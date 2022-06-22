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
        private RequestId $id,
        private RequestProject $project,
        private RequestUser $user,
        private RequestStatus $status,
        private RequestChangeDate $changeDate
    ) {
    }

    public static function create(RequestId $id, RequestProject $project, RequestUser $requestUser): self
    {
        $status = new PendingRequestStatus();
        $changeDate = new RequestChangeDate(date('c'));
        $request = new self($id, $project, $requestUser, $status, $changeDate);
        $project->setRequest($request);

        $request->registerEvent(new RequestWasCreatedEvent(
            $request->getId()->value,
            $request->getProject()->getId()->value,
            $request->getUser()->userId->value,
        ));

        return $request;
    }

    public function changeStatus(
        RequestStatus $status,
        UserId        $currentUserId
    ): void {
        $this->getProject()->ensureCanPerformOwnerOperations($currentUserId);

        $this->getStatus()->ensureCanBeChangedTo($status);
        $this->status = $status;
        $this->addParticipantIfNeeded($status);

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

    public function getProject(): RequestProject
    {
        return $this->project;
    }

    public function getStatus(): RequestStatus
    {
        return $this->status;
    }

    public function getChangeDate(): RequestChangeDate
    {
        return $this->changeDate;
    }

    private function addParticipantIfNeeded(RequestStatus $status): void
    {
        if ($status instanceof ConfirmedRequestStatus) {
            $this->getProject()->addParticipantFromRequest($this);
            $this->registerEvent(new ProjectParticipantWasAddedEvent(
                $this->getProject()->getId()->value,
                $this->getUser()->userId->value
            ));
        }
    }
}
