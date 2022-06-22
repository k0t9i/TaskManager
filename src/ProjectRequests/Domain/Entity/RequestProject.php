<?php
declare(strict_types=1);

namespace App\ProjectRequests\Domain\Entity;

use App\ProjectRequests\Domain\Collection\RequestCollection;
use App\ProjectRequests\Domain\Exception\RequestNotExistException;
use App\Projects\Domain\Collection\ProjectParticipantCollection;
use App\Projects\Domain\Exception\UserIsAlreadyOwnerException;
use App\Projects\Domain\Exception\UserIsAlreadyParticipantException;
use App\Projects\Domain\Exception\UserIsNotOwnerException;
use App\Projects\Domain\ValueObject\ProjectId;
use App\Projects\Domain\ValueObject\ProjectName;
use App\Projects\Domain\ValueObject\ProjectOwner;
use App\Projects\Domain\ValueObject\ProjectStatus;
use App\Users\Domain\ValueObject\UserId;


final class RequestProject
{
    /**
     * @var Request[]|RequestCollection
     */
    private RequestCollection $requests;

    public function __construct(
        private ProjectId $id,
        private ProjectName $name,
        private ProjectStatus $status,
        private ProjectOwner $owner,
        private ProjectParticipantCollection $participants
    ) {
    }

    public function addRequest(Request $request): void
    {
        $this->getStatus()->ensureAllowsModification();
        $this->ensureUserIsNotAlreadyInProject($request->getUser()->userId);
        $this->requests[$request->id->value] = $request;
    }

    public function addParticipantFromRequest(Request $request): void
    {
        $this->getStatus()->ensureAllowsModification();
        if (!array_key_exists($request->getId()->value, $this->requests)) {
            throw new RequestNotExistException();
        }

        $participant = $request->getUser();
        $this->ensureUserIsNotAlreadyInProject($participant->userId);
        $this->participants[$participant->userId->value] = $participant;
    }

    public function ensureCanPerformOwnerOperations(UserId $userId): void
    {
        $this->getStatus()->ensureAllowsModification();

        if (!$this->isOwner($userId)) {
            throw new UserIsNotOwnerException();
        }
    }

    public function getId(): ProjectId
    {
        return $this->id;
    }

    private function getStatus(): ProjectStatus
    {
        return $this->status;
    }

    private function getName(): ProjectName
    {
        return $this->name;
    }

    private function isOwner(UserId $userId): bool
    {
        return $this->owner->userId->value === $userId->value;
    }

    private function isParticipant(UserId $userId): bool
    {
        return isset($this->participants[$userId->value]);
    }

    private function ensureUserIsNotAlreadyInProject(UserId $userId): void
    {
        if ($this->isParticipant($userId)) {
            throw new UserIsAlreadyParticipantException();
        }
        if ($this->isOwner($userId)) {
            throw new UserIsAlreadyOwnerException();
        }
    }
}
