<?php
declare(strict_types=1);

namespace App\ProjectRequests\Domain\Entity;

use App\ProjectRequests\Domain\Collection\RequestCollection;
use App\ProjectRequests\Domain\Event\ProjectParticipantWasAddedEvent;
use App\ProjectRequests\Domain\Event\RequestStatusWasChangedEvent;
use App\ProjectRequests\Domain\Event\RequestWasCreatedEvent;
use App\ProjectRequests\Domain\Exception\ProjectRequestNotExistsException;
use App\ProjectRequests\Domain\Exception\UserAlreadyHasNonRejectedProjectRequestException;
use App\ProjectRequests\Domain\ValueObject\ProjectRequestId;
use App\ProjectRequests\Domain\ValueObject\RequestId;
use App\ProjectRequests\Domain\ValueObject\RequestStatus;
use App\Projects\Domain\ValueObject\ProjectStatus;
use App\Shared\Domain\Aggregate\AggregateRoot;
use App\Shared\Domain\Collection\UserIdCollection;
use App\Shared\Domain\Exception\UserIsAlreadyOwnerException;
use App\Shared\Domain\Exception\UserIsAlreadyParticipantException;
use App\Shared\Domain\Exception\UserIsNotOwnerException;
use App\Shared\Domain\ValueObject\UserId;

final class ProjectRequest extends AggregateRoot
{
    public function __construct(
        private ProjectRequestId $id,
        private ProjectStatus $status,
        private string $name,
        private UserId $ownerId,
        private UserIdCollection $participantIds,
        private RequestCollection $requests
    ) {
    }

    public function createRequest(RequestId $id, UserId $requestUserId): Request
    {
        $request = Request::create($id, $requestUserId);
        $this->addRequest($request);

        $this->registerEvent(new RequestWasCreatedEvent(
            $id->value,
            $this->getId()->value,
            $requestUserId->value,
        ));

        return $request;
    }

    public function changeStatus(
        RequestId $requestId,
        RequestStatus $status,
        UserId $currentUserId
    ): void {
        $this->getStatus()->ensureAllowsModification();
        if (!$this->isOwner($currentUserId)) {
            throw new UserIsNotOwnerException();
        }
        if (!$this->getRequests()->exists($requestId)) {
            throw new ProjectRequestNotExistsException();
        }

        /** @var Request $request */
        $request = $this->getRequests()->get($requestId->getHash());
        $request->changeStatus($status);

        if ($request->isConfirmed()) {
            $this->addParticipantFromRequest($request->getUserId());
            $this->registerEvent(new ProjectParticipantWasAddedEvent(
                $this->getId()->value,
                $request->getUserId()->value
            ));
        }

        $this->registerEvent(new RequestStatusWasChangedEvent(
            $this->getId()->value,
            $request->getStatus()->getScalar()
        ));
    }

    public function addRequest(Request $request): void
    {
        $this->getStatus()->ensureAllowsModification();
        $this->ensureIsUserAlreadyInProject($request->getUserId());
        $this->ensureDoesUserAlreadyHaveNonRejectedRequest($request->getUserId());
        $this->getRequests()->add($request);
    }

    public function getId(): ProjectRequestId
    {
        return $this->id;
    }

    public function getStatus(): ProjectStatus
    {
        return $this->status;
    }

    public function getName(): string
    {
        return $this->name;
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
        $this->getStatus()->ensureAllowsModification();
        $this->ensureIsUserAlreadyInProject($participantId);
        $this->getParticipantIds()->add($participantId);
    }

    private function ensureDoesUserAlreadyHaveNonRejectedRequest(UserId $userId): void
    {
        /** @var Request $request */
        foreach ($this->getRequests() as $request) {
            if ($request->isNonRejected() && $request->getUserId()->isEqual($userId)) {
                throw new UserAlreadyHasNonRejectedProjectRequestException();
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
        return $this->getParticipantIds()->exists($userId);
    }
}
