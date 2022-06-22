<?php
declare(strict_types=1);

namespace App\ProjectRequests\Domain\Entity;

use App\Projects\Domain\Collection\ProjectParticipantCollection;
use App\Projects\Domain\Exception\UserIsAlreadyOwnerException;
use App\Projects\Domain\Exception\UserIsAlreadyParticipantException;
use App\Projects\Domain\Exception\UserIsNotOwnerException;
use App\Projects\Domain\ValueObject\ProjectId;
use App\Projects\Domain\ValueObject\ProjectName;
use App\Projects\Domain\ValueObject\ProjectOwner;
use App\Projects\Domain\ValueObject\ProjectParticipant;
use App\Projects\Domain\ValueObject\ProjectStatus;
use App\Users\Domain\ValueObject\UserId;


final class RequestProject
{
    private Request $request;

    public function __construct(
        private ProjectId                    $id,
        private ProjectName                  $name,
        private ProjectStatus                $status,
        private ProjectOwner                 $owner,
        private ProjectParticipantCollection $participants,
    )
    {
    }

    public function setRequest(Request $request): void
    {
        $this->getStatus()->ensureAllowsModification();
        $this->ensureUserIsNotAlreadyInProject($request->getUser()->userId);
        $this->request = $request;
    }

    public function addParticipantFromRequest(): void
    {
        $this->getStatus()->ensureAllowsModification();

        $participant = $this->getRequest()->getUser();
        $this->ensureUserIsNotAlreadyInProject($participant->userId);
        $participants = $this->getParticipants();
        $participants[$participant->userId->value] = $participant;
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

    public function getStatus(): ProjectStatus
    {
        return $this->status;
    }

    public function getName(): ProjectName
    {
        return $this->name;
    }

    public function getOwner(): ProjectOwner
    {
        return $this->owner;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * @return ProjectParticipantCollection|ProjectParticipant[]
     */
    public function getParticipants(): ProjectParticipantCollection
    {
        return $this->participants;
    }

    private function isOwner(UserId $userId): bool
    {
        return $this->getOwner()->userId->value === $userId->value;
    }

    private function isParticipant(UserId $userId): bool
    {
        $participants = $this->getParticipants();
        return isset($participants[$userId->value]);
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
