<?php
declare(strict_types=1);

namespace App\Requests\Domain\Entity;

use App\Requests\Domain\Collection\RequestCollection;
use App\Requests\Domain\Event\RequestStatusWasChangedEvent;
use App\Requests\Domain\Event\RequestWasCreatedEvent;
use App\Requests\Domain\Exception\RequestNotExistsException;
use App\Requests\Domain\Exception\UserAlreadyHasPendingRequestException;
use App\Requests\Domain\ValueObject\PendingRequestStatus;
use App\Requests\Domain\ValueObject\RequestId;
use App\Requests\Domain\ValueObject\RequestManagerId;
use App\Requests\Domain\ValueObject\RequestStatus;
use App\Shared\Domain\Aggregate\AggregateRoot;
use App\Shared\Domain\Collection\UserIdCollection;
use App\Shared\Domain\Event\ProjectParticipantWasAddedEvent;
use App\Shared\Domain\Exception\UserIsAlreadyOwnerException;
use App\Shared\Domain\Exception\UserIsAlreadyParticipantException;
use App\Shared\Domain\Exception\UserIsNotOwnerException;
use App\Shared\Domain\ValueObject\DateTime;
use App\Shared\Domain\ValueObject\ProjectId;
use App\Shared\Domain\ValueObject\ProjectStatus;
use App\Shared\Domain\ValueObject\UserId;

final class RequestManager extends AggregateRoot
{
    public function __construct(
        private RequestManagerId  $id,
        private ProjectId         $projectId,
        private ProjectStatus     $status,
        private UserId            $ownerId,
        private UserIdCollection  $participantIds,
        private RequestCollection $requests
    ) {
    }

    public function createRequest(
        RequestId $id,
        UserId    $userId,
    ): Request {
        $status = new PendingRequestStatus();
        $changeDate = new DateTime();
        $request = new Request($id, $userId, $status, $changeDate);

        $this->ensureCanAddRequest($request->getUserId());

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
        $this->ensureCanChangeRequest($currentUserId);
        if (!$this->requests->hashExists($id->getHash())) {
            throw new RequestNotExistsException($id->value);
        }
        /** @var Request $request */
        $request = $this->requests->get($id->getHash());
        $request->changeStatus($status);

        if ($request->isConfirmed()) {
            $this->addParticipantFromRequest($request->getUserId());
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

    public function getOwnerId(): UserId
    {
        return $this->ownerId;
    }

    public function getParticipantIds(): UserIdCollection
    {
        return $this->participantIds;
    }

    public function getRequests(): RequestCollection
    {
        return $this->requests;
    }

    public function getRequestsForOwner(UserId $userId): RequestCollection
    {
        if (!$this->isOwner($userId)) {
            throw new UserIsNotOwnerException($userId->value);
        }
        return $this->requests;
    }

    private function addParticipantFromRequest(UserId $participantId): void
    {
        $this->ensureIsUserAlreadyInProject($participantId);
        $this->participantIds = $this->participantIds->add($participantId);
    }

    private function ensureCanAddRequest(UserId $userId): void
    {
        $this->status->ensureAllowsModification();
        $this->ensureIsUserAlreadyInProject($userId);
        $this->ensureUserDoesNotHavePendingRequest($userId);
    }

    private function ensureCanChangeRequest(UserId $userId): void
    {
        $this->status->ensureAllowsModification();
        if (!$this->isOwner($userId)) {
            throw new UserIsNotOwnerException($userId->value);
        }
    }

    private function ensureUserDoesNotHavePendingRequest(UserId $userId): void
    {
        /** @var Request $request */
        foreach ($this->requests as $request) {
            if ($request->isPending() && $request->getUserId()->isEqual($userId)) {
                throw new UserAlreadyHasPendingRequestException($userId->value, $this->projectId->value);
            }
        }
    }

    private function ensureIsUserAlreadyInProject(UserId $userId): void
    {
        if ($this->isParticipant($userId)) {
            throw new UserIsAlreadyParticipantException($userId->value);
        }
        if ($this->isOwner($userId)) {
            throw new UserIsAlreadyOwnerException($userId->value);
        }
    }

    private function isOwner(UserId $userId): bool
    {
        return $this->ownerId->isEqual($userId);
    }

    private function isParticipant(UserId $userId): bool
    {
        return $this->participantIds->exists($userId);
    }
}
