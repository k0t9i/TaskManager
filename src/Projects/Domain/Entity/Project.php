<?php
declare(strict_types=1);

namespace App\Projects\Domain\Entity;

use App\Projects\Domain\Collection\ProjectParticipantCollection;
use App\Projects\Domain\Event\ProjectInformationWasChangedEvent;
use App\Projects\Domain\Event\ProjectOwnerWasChangedEvent;
use App\Projects\Domain\Event\ProjectParticipantWasRemovedEvent;
use App\Projects\Domain\Event\ProjectStatusWasChangedEvent;
use App\Projects\Domain\Event\ProjectWasCreatedEvent;
use App\Projects\Domain\Exception\InsufficientPermissionsToChangeProjectParticipantException;
use App\Projects\Domain\Exception\ProjectOwnerOwnsProjectTaskException;
use App\Projects\Domain\Exception\ProjectParticipantNotExistException;
use App\Projects\Domain\Exception\ProjectTaskNotExistException;
use App\Projects\Domain\Exception\ProjectUserNotExistException;
use App\Projects\Domain\Exception\UserHasProjectTaskException;
use App\Projects\Domain\Exception\UserIsAlreadyOwnerException;
use App\Projects\Domain\Exception\UserIsNotOwnerException;
use App\Projects\Domain\Factory\ProjectStatusFactory;
use App\Projects\Domain\ValueObject\ActiveProjectStatus;
use App\Projects\Domain\ValueObject\ClosedProjectStatus;
use App\Projects\Domain\ValueObject\ProjectDescription;
use App\Projects\Domain\ValueObject\ProjectFinishDate;
use App\Projects\Domain\ValueObject\ProjectId;
use App\Projects\Domain\ValueObject\ProjectName;
use App\Projects\Domain\ValueObject\ProjectOwner;
use App\Projects\Domain\ValueObject\ProjectParticipant;
use App\Projects\Domain\ValueObject\ProjectStatus;
use App\Shared\Domain\Aggregate\AggregateRoot;
use App\Tasks\Domain\Entity\Task;
use App\Tasks\Domain\Event\TaskInformationWasChangedEvent;
use App\Tasks\Domain\Event\TaskStatusWasChangedEvent;
use App\Tasks\Domain\Event\TaskWasCreatedEvent;
use App\Tasks\Domain\Event\TaskWasDeletedEvent;
use App\Tasks\Domain\Exception\InsufficientPermissionsToChangeTaskException;
use App\Tasks\Domain\Exception\TaskFinishDateGreaterThanProjectFinishDateException;
use App\Tasks\Domain\Exception\TaskStartDateGreaterThanProjectFinishDateException;
use App\Tasks\Domain\Factory\TaskStatusFactory;
use App\Tasks\Domain\TaskCollection;
use App\Tasks\Domain\ValueObject\ActiveTaskStatus;
use App\Tasks\Domain\ValueObject\TaskBrief;
use App\Tasks\Domain\ValueObject\TaskDescription;
use App\Tasks\Domain\ValueObject\TaskFinishDate;
use App\Tasks\Domain\ValueObject\TaskId;
use App\Tasks\Domain\ValueObject\TaskName;
use App\Tasks\Domain\ValueObject\TaskStartDate;
use App\Tasks\Domain\ValueObject\TaskStatus;
use App\Users\Domain\Entity\User;
use App\Users\Domain\ValueObject\UserId;

final class Project extends AggregateRoot
{
    /**
     * @var ProjectParticipant[]|ProjectParticipantCollection
     */
    private ProjectParticipantCollection $participants;

    /**
     * @var Task[]|TaskCollection
     */
    private TaskCollection $tasks;

    public function __construct(
        private ProjectId $id,
        private ProjectName $name,
        private ProjectDescription $description,
        private ProjectFinishDate $finishDate,
        private ProjectStatus $status,
        private ProjectOwner $owner
    ) {
        $this->tasks = new TaskCollection();
        $this->participants = new ProjectParticipantCollection();
    }

    /**
     * @return ProjectId
     */
    public function getId(): ProjectId
    {
        return $this->id;
    }

    /**
     * @return ProjectName
     */
    public function getName(): ProjectName
    {
        return $this->name;
    }

    /**
     * @return ProjectDescription
     */
    public function getDescription(): ProjectDescription
    {
        return $this->description;
    }

    /**
     * @return ProjectFinishDate
     */
    public function getFinishDate(): ProjectFinishDate
    {
        return $this->finishDate;
    }

    public function getOwner(): ProjectOwner
    {
        return $this->owner;
    }

    /**
     * @return ProjectParticipant[]|ProjectParticipantCollection
     */
    public function getParticipants(): ProjectParticipantCollection
    {
        return $this->participants;
    }

    public function setParticipants(ProjectParticipantCollection $value): void
    {
        $this->participants = $value;
    }

    /**
     * @return Task[]
     */
    public function getTasks(): TaskCollection
    {
        return $this->tasks;
    }

    public function setTasks(TaskCollection $tasks): void
    {
        $this->tasks = $tasks;
    }

    public static function create(
        ProjectId $id,
        ProjectName $name,
        ProjectDescription $description,
        ProjectFinishDate $finishDate,
        ProjectOwner $owner
    ): self {
        $status = new ActiveProjectStatus();
        $project = new self($id, $name, $description, $finishDate, $status, $owner);

        $project->registerEvent(new ProjectWasCreatedEvent(
            $id->value,
            $name->value,
            $description->value,
            $finishDate->getValue(),
            ProjectStatusFactory::scalarFromObject($status),
            $owner->userId->value
        ));

        return $project;
    }

    public function ensureIsOwner(UserId $userId): void
    {
        if (!$this->isOwner($userId)) {
            throw new UserIsNotOwnerException();
        }
    }

    public function isOwner(UserId $userId): bool
    {
        return $this->owner->userId->isEqual($userId);
    }

    public function isParticipant(UserId $userId): bool
    {
        return $this->participants->hashExists($userId->getHash());
    }

    public function isUserInProject(UserId $userId): bool
    {
        return $this->isOwner($userId) || $this->isParticipant($userId);
    }

    public function changeOwner(ProjectOwner $owner, UserId $currentUserId): void
    {
        $this->getStatus()->ensureAllowsModification();
        $this->ensureIsOwner($currentUserId);

        if ($this->isOwner($owner->userId)) {
            throw new UserIsAlreadyOwnerException();
        }
        foreach ($this->tasks as $task) {
            if ($task->isOwner($this->owner->userId)) {
                throw new ProjectOwnerOwnsProjectTaskException();
            }
        }
        $this->owner = $owner;

        $this->registerEvent(new ProjectOwnerWasChangedEvent(
            $this->getId()->value,
            $this->owner->userId->value
        ));
    }

    public function removeParticipant(ProjectParticipant $participant, UserId $currentUserId): void
    {
        $this->getStatus()->ensureAllowsModification();
        $this->ensureCanChangeProjectParticipant($participant->userId, $currentUserId);

        if (!$this->isParticipant($participant->userId)) {
            throw new ProjectParticipantNotExistException();
        }
        foreach ($this->tasks as $task) {
            if ($task->isOwner($participant->userId)) {
                throw new UserHasProjectTaskException();
            }
        }

        $this->participants->remove($participant);

        $this->registerEvent(new ProjectParticipantWasRemovedEvent(
            $this->getId()->value,
            $participant->userId->value
        ));
    }

    public function changeInformation(
        ProjectName $name,
        ProjectDescription $description,
        ProjectFinishDate $finishDate,
        UserId $currentUserId
    ): void {
        $this->getStatus()->ensureAllowsModification();
        $this->ensureIsOwner($currentUserId);

        $this->name = $name;
        $this->description = $description;
        $this->finishDate = $finishDate;

        foreach ($this->tasks as $task) {
            $task->limitDatesByProjectFinishDate($this);
        }

        $this->registerEvent(new ProjectInformationWasChangedEvent(
            $this->getId()->value,
            $this->name->value,
            $this->description->value,
            $this->finishDate->getValue()
        ));
    }

    /**
     * @return ProjectStatus
     */
    public function getStatus(): ProjectStatus
    {
        return $this->status;
    }

    /**
     * @param ProjectStatus $status
     */
    public function changeStatus(ProjectStatus $status, UserId $currentUserId): void
    {
        $this->getStatus()->ensureCanBeChangedTo($status);
        $this->ensureIsOwner($currentUserId);

        if ($status instanceof ClosedProjectStatus) {
            foreach ($this->tasks as $task) {
                $task->closeTaskIfProjectWasClosed($this);
            }
        }
        $this->status = $status;

        $this->registerEvent(new ProjectStatusWasChangedEvent(
            $this->getId()->value,
            ProjectStatusFactory::scalarFromObject($status)
        ));
    }

    public function createTask(
        TaskId $id,
        TaskName $name,
        TaskBrief $brief,
        TaskDescription $description,
        TaskStartDate $startDate,
        TaskFinishDate $finishDate,
        User $owner,
        UserId $currentUserId
    ): void {
        $this->getStatus()->ensureAllowsModification();
        $this->ensureCanChangeTask($owner->getId(), $currentUserId);

        if ($startDate->isGreaterThan($this->finishDate)) {
            throw new TaskStartDateGreaterThanProjectFinishDateException();
        }
        if ($finishDate->isGreaterThan($this->finishDate)) {
            throw new TaskFinishDateGreaterThanProjectFinishDateException();
        }
        if (!$this->isUserInProject($owner->getId())) {
            throw new ProjectUserNotExistException();
        }

        $status = new ActiveTaskStatus();
        $task = new Task($id, $name, $brief, $description, $startDate, $finishDate, $owner, $status, $this);
        $this->tasks->add($task);

        $this->registerEvent(new TaskWasCreatedEvent(
            $id->value,
            $name->value,
            $brief->value,
            $description->value,
            $startDate->getValue(),
            $finishDate->getValue(),
            $owner->getId()->value,
            TaskStatusFactory::scalarFromObject($status),
            $this->id->value,
        ));
    }

    public function changeTaskInformation(
        TaskId $id,
        TaskName $name,
        TaskBrief $brief,
        TaskDescription $description,
        TaskStartDate $startDate,
        TaskFinishDate $finishDate,
        UserId $currentUserId
    ): void {
        $this->getStatus()->ensureAllowsModification();

        if ($startDate->isGreaterThan($this->getFinishDate())) {
            throw new TaskStartDateGreaterThanProjectFinishDateException();
        }
        if ($finishDate->isGreaterThan($this->getFinishDate())) {
            throw new TaskFinishDateGreaterThanProjectFinishDateException();
        }
        $this->ensureProjectTaskExits($id);
        /** @var Task $task */
        $task = $this->tasks->get($id->getHash());
        $this->ensureCanChangeTask($task->getOwner()->getId(), $currentUserId);

        $task->changeInformation($name, $brief, $description, $startDate, $finishDate);

        $this->registerEvent(new TaskInformationWasChangedEvent(
            $id->value,
            $name->value,
            $brief->value,
            $description->value,
            $startDate->getValue(),
            $finishDate->getValue(),
            $this->id->value,
        ));
    }

    public function deleteTask(TaskId $id, UserId $currentUserId): void
    {
        $this->getStatus()->ensureAllowsModification();
        $this->ensureProjectTaskExits($id);

        /** @var Task $task */
        $task = $this->tasks->get($id->getHash());
        $this->ensureCanChangeTask($task->getOwner()->getId(), $currentUserId);

        $this->tasks->remove($task);

        $this->registerEvent(new TaskWasDeletedEvent(
            $id->value
        ));
    }

    public function changeTaskStatus(TaskId $id, TaskStatus $status, UserId $currentUserId): void
    {
        $this->getStatus()->ensureAllowsModification();
        $this->ensureProjectTaskExits($id);
        /** @var Task $task */
        $task = $this->tasks->get($id->getHash());
        $this->ensureCanChangeTask($task->getOwner()->getId(), $currentUserId);

        $task->changeStatus($status);

        $this->registerEvent(new TaskStatusWasChangedEvent(
            $task->getId()->value,
            TaskStatusFactory::scalarFromObject($status)
        ));
    }

    private function ensureCanChangeProjectParticipant(UserId $participantId, UserId $currentUserId): void
    {
        if (!$this->isOwner($currentUserId) && $participantId->value !== $currentUserId->value) {
            throw new InsufficientPermissionsToChangeProjectParticipantException();
        }
    }

    private function ensureCanChangeTask(UserId $taskOwnerId, UserId $currentUserId): void
    {
        if (!$this->isOwner($currentUserId) && $taskOwnerId->value !== $currentUserId->value) {
            throw new InsufficientPermissionsToChangeTaskException();
        }
    }

    private function ensureProjectTaskExits(TaskId $taskId): void
    {
        if (!array_key_exists($taskId->value, $this->tasks)) {
            throw new ProjectTaskNotExistException();
        }
    }
}