<?php
declare(strict_types=1);

namespace App\ProjectMemberships\Domain\Entity;

use App\ProjectMemberships\Domain\Event\ProjectOwnerWasChangedEvent;
use App\ProjectMemberships\Domain\Event\ProjectParticipantWasRemovedEvent;
use App\ProjectMemberships\Domain\Exception\InsufficientPermissionsToChangeProjectParticipantException;
use App\ProjectMemberships\Domain\Exception\ProjectOwnerOwnsProjectTaskException;
use App\ProjectMemberships\Domain\Exception\ProjectParticipantNotExistException;
use App\ProjectMemberships\Domain\Exception\UserHasProjectTaskException;
use App\ProjectMemberships\Domain\ValueObject\MembershipId;
use App\Projects\Domain\Exception\UserIsAlreadyOwnerException;
use App\Projects\Domain\Exception\UserIsNotOwnerException;
use App\Projects\Domain\ValueObject\ProjectStatus;
use App\Shared\Domain\Aggregate\AggregateRoot;
use App\Shared\Domain\Collection\UserIdCollection;
use App\Shared\Domain\ValueObject\UserId;

final class Membership extends AggregateRoot
{
    public function __construct(
        private MembershipId $id,
        private ProjectStatus $status,
        private UserId $ownerId,
        private UserIdCollection $participantIds,
        private UserIdCollection $taskOwnerIds
    ) {
    }

    public function removeParticipant(UserId $participantId, UserId $currentUserId): void
    {
        $this->getStatus()->ensureAllowsModification();
        $this->ensureCanChangeProjectParticipant($participantId, $currentUserId);

        if (!$this->isParticipant($participantId)) {
            throw new ProjectParticipantNotExistException();
        }
        /** @var UserId $taskOwnerId */
        foreach ($this->taskOwnerIds as $taskOwnerId) {
            if ($taskOwnerId->isEqual($participantId)) {
                throw new UserHasProjectTaskException();
            }
        }

        $this->participantIds->remove($participantId);

        $this->registerEvent(new ProjectParticipantWasRemovedEvent(
            $this->getId()->value,
            $participantId->value
        ));
    }

    public function changeOwner(UserId $ownerId, UserId $currentUserId): void
    {
        $this->getStatus()->ensureAllowsModification();
        $this->ensureIsOwner($currentUserId);

        if ($this->isOwner($ownerId)) {
            throw new UserIsAlreadyOwnerException();
        }
        /** @var UserId $taskOwnerId */
        foreach ($this->taskOwnerIds as $taskOwnerId) {
            if ($taskOwnerId->isEqual($this->ownerId)) {
                throw new ProjectOwnerOwnsProjectTaskException();
            }
        }
        $this->ownerId = $ownerId;

        $this->registerEvent(new ProjectOwnerWasChangedEvent(
            $this->getId()->value,
            $this->ownerId->value
        ));
    }

    public function getId(): MembershipId
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

    public function getTaskOwnerIds(): UserIdCollection
    {
        return $this->taskOwnerIds;
    }

    public function isOwner(UserId $userId): bool
    {
        return $this->ownerId->isEqual($userId);
    }

    private function isParticipant(UserId $userId): bool
    {
        return $this->participantIds->hashExists($userId->getHash());
    }

    private function ensureIsOwner(UserId $userId): void
    {
        if (!$this->isOwner($userId)) {
            throw new UserIsNotOwnerException();
        }
    }

    private function ensureCanChangeProjectParticipant(UserId $participantId, UserId $currentUserId): void
    {
        if (!$this->isOwner($currentUserId) && $participantId->value !== $currentUserId->value) {
            throw new InsufficientPermissionsToChangeProjectParticipantException();
        }
    }
}
