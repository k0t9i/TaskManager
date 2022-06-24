<?php
declare(strict_types=1);

namespace App\ProjectTasks\Domain\Entity;

use App\Projects\Domain\Exception\ProjectTaskNotExistException;
use App\Projects\Domain\Exception\ProjectUserNotExistException;
use App\Projects\Domain\ValueObject\ProjectFinishDate;
use App\Projects\Domain\ValueObject\ProjectStatus;
use App\ProjectTasks\Domain\Collection\TaskCollection;
use App\ProjectTasks\Domain\Event\TaskInformationWasChangedEvent;
use App\ProjectTasks\Domain\Event\TaskStatusWasChangedEvent;
use App\ProjectTasks\Domain\Event\TaskWasCreatedEvent;
use App\ProjectTasks\Domain\Event\TaskWasDeletedEvent;
use App\ProjectTasks\Domain\Exception\InsufficientPermissionsToChangeTaskException;
use App\ProjectTasks\Domain\Exception\TaskFinishDateGreaterThanProjectFinishDateException;
use App\ProjectTasks\Domain\Exception\TaskStartDateGreaterThanProjectFinishDateException;
use App\ProjectTasks\Domain\ValueObject\ActiveTaskStatus;
use App\ProjectTasks\Domain\ValueObject\ProjectTaskId;
use App\ProjectTasks\Domain\ValueObject\TaskBrief;
use App\ProjectTasks\Domain\ValueObject\TaskDescription;
use App\ProjectTasks\Domain\ValueObject\TaskFinishDate;
use App\ProjectTasks\Domain\ValueObject\TaskId;
use App\ProjectTasks\Domain\ValueObject\TaskName;
use App\ProjectTasks\Domain\ValueObject\TaskStartDate;
use App\ProjectTasks\Domain\ValueObject\TaskStatus;
use App\Shared\Domain\Aggregate\AggregateRoot;
use App\Shared\Domain\Collection\UserIdCollection;
use App\Shared\Domain\ValueObject\UserId;

final class ProjectTask extends AggregateRoot
{
    public function __construct(
        private ProjectTaskId $id,
        private ProjectStatus $status,
        private UserId $ownerId,
        private ProjectFinishDate $finishDate,
        private UserIdCollection $participantIds,
        private TaskCollection $tasks
    ) {
    }

    public function createTask(
        TaskId $id,
        TaskName $name,
        TaskBrief $brief,
        TaskDescription $description,
        TaskStartDate $startDate,
        TaskFinishDate $finishDate,
        UserId $ownerId,
        UserId $currentUserId
    ): void {
        $this->getStatus()->ensureAllowsModification();
        $this->ensureCanChangeTask($ownerId, $currentUserId);

        if ($startDate->isGreaterThan($this->finishDate)) {
            throw new TaskStartDateGreaterThanProjectFinishDateException();
        }
        if ($finishDate->isGreaterThan($this->finishDate)) {
            throw new TaskFinishDateGreaterThanProjectFinishDateException();
        }
        if (!$this->isUserInProject($ownerId)) {
            throw new ProjectUserNotExistException();
        }

        $status = new ActiveTaskStatus();
        $task = new Task($id, $name, $brief, $description, $startDate, $finishDate, $ownerId, $status);
        $this->tasks->add($task);

        $this->registerEvent(new TaskWasCreatedEvent(
            $id->value,
            $name->value,
            $brief->value,
            $description->value,
            $startDate->getValue(),
            $finishDate->getValue(),
            $ownerId->value,
            $status->getScalar(),
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
        $this->ensureCanChangeTask($task->getOwnerId(), $currentUserId);

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
        $this->ensureCanChangeTask($task->getOwnerId(), $currentUserId);

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
        $this->ensureCanChangeTask($task->getOwnerId(), $currentUserId);

        $task->changeStatus($status);

        $this->registerEvent(new TaskStatusWasChangedEvent(
            $task->getId()->value,
            $status->getScalar()
        ));
    }

    public function getId(): ProjectTaskId
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

    public function getFinishDate(): ProjectFinishDate
    {
        return $this->finishDate;
    }

    public function getParticipantIds(): UserIdCollection
    {
        return $this->participantIds;
    }

    public function getTasks(): TaskCollection
    {
        return $this->tasks;
    }

    private function ensureCanChangeTask(UserId $taskOwnerId, UserId $currentUserId): void
    {
        if (!$this->isOwner($currentUserId) && $taskOwnerId->value !== $currentUserId->value) {
            throw new InsufficientPermissionsToChangeTaskException();
        }
    }

    private function isUserInProject(UserId $userId): bool
    {
        return $this->isOwner($userId) || $this->isParticipant($userId);
    }

    private function isOwner(UserId $userId): bool
    {
        return $this->ownerId->isEqual($userId);
    }

    private function isParticipant(UserId $userId): bool
    {
        return $this->participantIds->hashExists($userId->getHash());
    }

    private function ensureProjectTaskExits(TaskId $taskId): void
    {
        if ($this->tasks->hashExists($taskId->getHash())) {
            throw new ProjectTaskNotExistException();
        }
    }
}
