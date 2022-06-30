<?php
declare(strict_types=1);

namespace App\Projects\Domain\Entity;

use App\Projects\Domain\Event\ProjectInformationWasChangedEvent;
use App\Projects\Domain\Event\ProjectOwnerWasChangedEvent;
use App\Projects\Domain\Event\ProjectParticipantWasRemovedEvent;
use App\Projects\Domain\Event\ProjectStatusWasChangedEvent;
use App\Projects\Domain\Event\ProjectWasCreatedEvent;
use App\Projects\Domain\Exception\InsufficientPermissionsToChangeProjectParticipantException;
use App\Projects\Domain\ValueObject\ProjectInformation;
use App\Projects\Domain\ValueObject\ProjectOwner;
use App\Projects\Domain\ValueObject\ProjectParticipants;
use App\Projects\Domain\ValueObject\ProjectTasks;
use App\Shared\Domain\Aggregate\AggregateRoot;
use App\Shared\Domain\ValueObject\ActiveProjectStatus;
use App\Shared\Domain\ValueObject\ClosedProjectStatus;
use App\Shared\Domain\ValueObject\ProjectId;
use App\Shared\Domain\ValueObject\ProjectStatus;
use App\Shared\Domain\ValueObject\UserId;
use Exception;

final class Project extends AggregateRoot
{
    // TODO add task TaskWasCreatedEvent
    // TODO change task status TaskStatusWasChangedEvent
    // TODO change task information TaskInformationWasChangedEvent
    // TODO add participant ProjectParticipantWasAddedEvent
    public function __construct(
        private ProjectId $id,
        private ProjectInformation $information,
        private ProjectStatus $status,
        private ProjectOwner $owner,
        private ProjectParticipants $participants,
        private ProjectTasks $tasks
    ) {
    }

    public static function create(
        ProjectId $id,
        ProjectInformation $information,
        ProjectOwner $owner
    ): self {
        $status = new ActiveProjectStatus();
        $project = new self(
            $id,
            $information,
            $status,
            $owner,
            new ProjectParticipants(),
            new ProjectTasks()
        );

        $project->registerEvent(new ProjectWasCreatedEvent(
            $id->value,
            $information->name->value,
            $information->description->value,
            $information->finishDate->getValue(),
            (string) $status->getScalar(),
            $owner->userId->value
        ));

        return $project;
    }

    public function changeInformation(
        ProjectInformation $information,
        UserId $currentUserId
    ): void {
        $this->status->ensureAllowsModification();
        $this->owner->ensureIsOwner($currentUserId);

        $this->information = $information;

        $this->tasks->limitDatesOfAllTasksByProjectFinishDate($this);

        $this->registerEvent(new ProjectInformationWasChangedEvent(
            $this->id->value,
            $information->name->value,
            $information->description->value,
            $information->finishDate->getValue()
        ));
    }

    /**
     * @param ProjectStatus $status
     */
    public function changeStatus(ProjectStatus $status, UserId $currentUserId): void
    {
        $this->status->ensureCanBeChangedTo($status);
        $this->owner->ensureIsOwner($currentUserId);

        $this->status = $status;
        if ($this->status instanceof ClosedProjectStatus) {
            $this->tasks->closeAllTasksIfActive($this);
        }

        $this->registerEvent(new ProjectStatusWasChangedEvent(
            $this->id->value,
            (string) $status->getScalar()
        ));
    }

    /**
     * @param UserId $participantId
     * @param UserId $currentUserId
     * @throws Exception
     */
    public function removeParticipant(UserId $participantId, UserId $currentUserId): void
    {
        $this->status->ensureAllowsModification();
        if (!$this->owner->isOwner($currentUserId) && !$participantId->isEqual($currentUserId)) {
            throw new InsufficientPermissionsToChangeProjectParticipantException();
        }
        $this->participants->ensureIsParticipant($participantId);
        $this->tasks->ensureDoesUserHaveTask($participantId);

        $this->participants->remove($participantId);

        $this->registerEvent(new ProjectParticipantWasRemovedEvent(
            $this->id->value,
            $participantId->value
        ));
    }

    /**
     * @param UserId $ownerId
     * @param UserId $currentUserId
     * @throws Exception
     */
    public function changeOwner(UserId $ownerId, UserId $currentUserId): void
    {
        $this->status->ensureAllowsModification();
        $this->owner->ensureIsOwner($currentUserId);

        $this->owner->ensureIsNotOwner($ownerId);
        $this->participants->ensureIsNotParticipant($ownerId);
        $this->tasks->ensureDoesUserHaveTask($this->owner->userId);

        $this->owner = new ProjectOwner($ownerId);

        $this->registerEvent(new ProjectOwnerWasChangedEvent(
            $this->id->value,
            $this->owner->userId->value
        ));
    }

    public function getId(): ProjectId
    {
        return $this->id;
    }

    public function getInformation(): ProjectInformation
    {
        return $this->information;
    }

    public function getStatus(): ProjectStatus
    {
        return $this->status;
    }

    public function getOwner(): ProjectOwner
    {
        return $this->owner;
    }

    public function getParticipants(): ProjectParticipants
    {
        return $this->participants;
    }
}