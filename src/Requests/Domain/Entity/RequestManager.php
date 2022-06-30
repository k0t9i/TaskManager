<?php
declare(strict_types=1);

namespace App\Requests\Domain\Entity;

use App\Requests\Domain\Collection\RequestCollection;
use App\Requests\Domain\Event\ProjectParticipantWasAddedEvent;
use App\Requests\Domain\Event\RequestStatusWasChangedEvent;
use App\Requests\Domain\Event\RequestWasCreatedEvent;
use App\Requests\Domain\Exception\RequestNotExistsException;
use App\Requests\Domain\Exception\UserAlreadyHasNonRejectedRequestException;
use App\Requests\Domain\ValueObject\PendingRequestStatus;
use App\Requests\Domain\ValueObject\RequestId;
use App\Requests\Domain\ValueObject\RequestManagerId;
use App\Requests\Domain\ValueObject\RequestStatus;
use App\Shared\Domain\Aggregate\AggregateRoot;
use App\Shared\Domain\Collection\UserIdCollection;
use App\Shared\Domain\Exception\UserIsAlreadyOwnerException;
use App\Shared\Domain\Exception\UserIsAlreadyParticipantException;
use App\Shared\Domain\Exception\UserIsNotOwnerException;
use App\Shared\Domain\ValueObject\DateTime;
use App\Shared\Domain\ValueObject\ProjectId;
use App\Shared\Domain\ValueObject\ProjectStatus;
use App\Shared\Domain\ValueObject\UserId;

final class RequestManager extends AggregateRoot
{
    //TODO add project ProjectWasCreatedEvent
    //TODO change project status ProjectStatusWasChangedEvent
    //TODO change project owner ProjectOwnerWasChangedEvent
    //TODO remove project participant ProjectParticipantWasRemovedEvent
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
        UserId $requestUserId,
    ): Request {
        $status = new PendingRequestStatus();
        $changeDate = new DateTime();
        $request = new Request($id, $requestUserId, $status, $changeDate);

        $this->ensureCanAddRequest($request->getUserId());

        $this->registerEvent(new RequestWasCreatedEvent(
            $this->id->value,
            $request->getId()->value,
            $requestUserId->value,
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
            throw new RequestNotExistsException();
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

    private function addParticipantFromRequest(UserId $participantId): void
    {
        $this->ensureIsUserAlreadyInProject($participantId);
        $this->participantIds->add($participantId);
    }

    private function ensureCanAddRequest(UserId $userId): void
    {
        $this->status->ensureAllowsModification();
        $this->ensureIsUserAlreadyInProject($userId);
        $this->ensureUserDoesNotHaveNonRejectedRequest($userId);
    }

    private function ensureCanChangeRequest(UserId $userId): void
    {
        $this->status->ensureAllowsModification();
        if (!$this->isOwner($userId)) {
            throw new UserIsNotOwnerException();
        }
    }

    private function ensureUserDoesNotHaveNonRejectedRequest(UserId $userId): void
    {
        /** @var Request $request */
        foreach ($this->requests as $request) {
            if ($request->isNonRejected() && $request->getUserId()->isEqual($userId)) {
                throw new UserAlreadyHasNonRejectedRequestException();
            }
        }
    }

    private function ensureIsUserAlreadyInProject(UserId $userId): void
    {
        if ($this->isParticipant($userId)) {
            throw new UserIsAlreadyParticipantException();
        }
        if ($this->isOwner($userId)) {
            throw new UserIsAlreadyOwnerException();
        }
    }

    private function isOwner(UserId $userId): bool
    {
        return $this->getOwnerId()->isEqual($userId);
    }

    private function isParticipant(UserId $userId): bool
    {
        return $this->participantIds->exists($userId);
    }
}
