<?php
declare(strict_types=1);

namespace App\Requests\Domain\Entity;

use App\Requests\Domain\Collection\SameProjectRequestCollection;
use App\Requests\Domain\Exception\UserAlreadyHasNonRejectedRequestException;
use App\Requests\Domain\ValueObject\RequestProjectId;
use App\Shared\Domain\Collection\UserIdCollection;
use App\Shared\Domain\Exception\UserIsAlreadyOwnerException;
use App\Shared\Domain\Exception\UserIsAlreadyParticipantException;
use App\Shared\Domain\Exception\UserIsNotOwnerException;
use App\Shared\Domain\ValueObject\ProjectStatus;
use App\Shared\Domain\ValueObject\UserId;

final class RequestProject
{
    //TODO change project status
    //TODO change project owner
    //TODO add project participant status
    //TODO remove project participant status
    public function __construct(
        private RequestProjectId             $id,
        private ProjectStatus                $status,
        private UserId                       $ownerId,
        private UserIdCollection             $participantIds,
        private SameProjectRequestCollection $requests
    ) {
    }

    public function getId(): RequestProjectId
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

    public function getRequests(): SameProjectRequestCollection
    {
        return $this->requests;
    }

    public function addParticipantFromRequest(UserId $participantId): void
    {
        $this->ensureIsUserAlreadyInProject($participantId);
        $this->participantIds->add($participantId);
    }

    public function ensureCanAddRequest(UserId $userId): void
    {
        $this->status->ensureAllowsModification();
        $this->ensureIsUserAlreadyInProject($userId);
        $this->ensureUserDoesNotHaveNonRejectedRequest($userId);
    }

    public function ensureCanChangeRequest(UserId $userId): void
    {
        $this->status->ensureAllowsModification();
        if (!$this->isOwner($userId)) {
            throw new UserIsNotOwnerException();
        }
    }

    private function ensureUserDoesNotHaveNonRejectedRequest(UserId $userId): void
    {
        /** @var SameProjectRequest $request */
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
