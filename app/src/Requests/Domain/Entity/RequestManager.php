<?php
declare(strict_types=1);

namespace App\Requests\Domain\Entity;

use App\Requests\Domain\Collection\RequestCollection;
use App\Requests\Domain\Event\RequestStatusWasChangedEvent;
use App\Requests\Domain\Event\RequestWasCreatedEvent;
use App\Requests\Domain\ValueObject\PendingRequestStatus;
use App\Requests\Domain\ValueObject\RequestId;
use App\Requests\Domain\ValueObject\RequestManagerId;
use App\Requests\Domain\ValueObject\Requests;
use App\Requests\Domain\ValueObject\RequestStatus;
use App\Shared\Domain\Aggregate\AggregateRoot;
use App\Shared\Domain\Event\ProjectParticipantWasAddedEvent;
use App\Shared\Domain\ValueObject\DateTime;
use App\Shared\Domain\ValueObject\Owner;
use App\Shared\Domain\ValueObject\Participants;
use App\Shared\Domain\ValueObject\ProjectId;
use App\Shared\Domain\ValueObject\ProjectStatus;
use App\Shared\Domain\ValueObject\UserId;

final class RequestManager extends AggregateRoot
{
    public function __construct(
        private RequestManagerId  $id,
        private ProjectId         $projectId,
        private ProjectStatus     $status,
        private Owner             $owner,
        private Participants      $participants,
        private Requests          $requests
    ) {
    }

    public function createRequest(
        RequestId $id,
        UserId    $userId,
    ): Request {
        $this->status->ensureAllowsModification();

        $status = new PendingRequestStatus();
        $changeDate = new DateTime();
        $request = new Request($id, $userId, $status, $changeDate);

        $this->ensureIsUserAlreadyInProject($userId);
        $this->requests->ensureUserDoesNotHavePendingRequest($userId, $this->projectId);

        $this->requests = $this->requests->add($request);

        $this->registerEvent(new RequestWasCreatedEvent(
            $this->id->value,
            $request->getId()->value,
            $userId->value,
        ));

        return $request;
    }

    public function changeRequestStatus(
        RequestId $id,
        RequestStatus $status,
        UserId $currentUserId
    ): void {
        $this->status->ensureAllowsModification();
        $this->owner->ensureIsOwner($currentUserId);
        $this->requests->ensureRequestExists($id);

        /** @var Request $request */
        $request = $this->requests->get($id);
        $request->changeStatus($status);

        if ($request->isConfirmed()) {
            $this->ensureIsUserAlreadyInProject($request->getUserId());
            $this->participants = $this->participants->add($request->getUserId());
            $this->registerEvent(new ProjectParticipantWasAddedEvent(
                $this->id->value,
                $this->projectId->value,
                $request->getUserId()->value
            ));
        }

        $this->registerEvent(new RequestStatusWasChangedEvent(
            $this->id->value,
            $request->getId()->value,
            (string) $this->status->getScalar()
        ));
    }

    public function getId(): RequestManagerId
    {
        return $this->id;
    }

    public function getProjectId(): ProjectId
    {
        return $this->projectId;
    }

    public function getStatus(): ProjectStatus
    {
        return $this->status;
    }

    public function getOwner(): Owner
    {
        return $this->owner;
    }

    public function getParticipants(): Participants
    {
        return $this->participants;
    }

    public function getRequests(): Requests
    {
        return $this->requests;
    }

    public function getRequestsForOwner(UserId $userId): RequestCollection
    {
        $this->owner->ensureIsNotOwner($userId);
        return $this->requests->getInnerItems();
    }

    private function ensureIsUserAlreadyInProject(UserId $userId): void
    {
        $this->participants->ensureIsNotParticipant($userId);
        $this->owner->ensureIsNotOwner($userId);
    }
}
