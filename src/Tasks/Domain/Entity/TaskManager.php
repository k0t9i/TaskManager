<?php
declare(strict_types=1);

namespace App\Tasks\Domain\Entity;

use App\Shared\Domain\Aggregate\AggregateRoot;
use App\Shared\Domain\Collection\UserIdCollection;
use App\Shared\Domain\ValueObject\ActiveTaskStatus;
use App\Shared\Domain\ValueObject\DateTime;
use App\Shared\Domain\ValueObject\ProjectId;
use App\Shared\Domain\ValueObject\ProjectStatus;
use App\Shared\Domain\ValueObject\TaskId;
use App\Shared\Domain\ValueObject\TaskStatus;
use App\Shared\Domain\ValueObject\UserId;
use App\Tasks\Domain\Collection\TaskCollection;
use App\Tasks\Domain\Collection\TaskLinkCollection;
use App\Tasks\Domain\Event\TaskInformationWasChangedEvent;
use App\Tasks\Domain\Event\TaskLinkWasAddedEvent;
use App\Tasks\Domain\Event\TaskLinkWasDeletedEvent;
use App\Tasks\Domain\Event\TaskStatusWasChangedEvent;
use App\Tasks\Domain\Event\TaskWasCreatedEvent;
use App\Tasks\Domain\Exception\InsufficientPermissionsToChangeTaskException;
use App\Tasks\Domain\Exception\TaskFinishDateGreaterThanProjectFinishDateException;
use App\Tasks\Domain\Exception\TaskNotExistException;
use App\Tasks\Domain\Exception\TaskStartDateGreaterThanProjectFinishDateException;
use App\Tasks\Domain\Exception\TaskUserNotExistException;
use App\Tasks\Domain\ValueObject\TaskInformation;
use App\Tasks\Domain\ValueObject\TaskManagerId;

final class TaskManager extends AggregateRoot
{
    //TODO add project ProjectWasCreatedEvent
    //TODO change project status ProjectStatusWasChangedEvent
    //TODO change project owner ProjectOwnerWasChangedEvent
    //TODO change project information ProjectInformationWasChangedEvent
    //TODO change project task start date ProjectTaskStartDateWasChangedEvent
    //TODO change project task finish date ProjectTaskFinishDateWasChangedEvent
    //TODO change project task status ProjectTaskStatusWasChangedEvent
    //TODO add project participant ProjectParticipantWasAddedEvent
    //TODO remove project participant ProjectParticipantWasRemovedEvent
    public function __construct(
        private TaskManagerId    $id,
        private ProjectId         $projectId,
        private ProjectStatus    $status,
        private UserId           $ownerId,
        private DateTime         $finishDate,
        private UserIdCollection $participantIds,
        private TaskCollection   $tasks
    ) {
    }

    public function createTask(
        TaskId $id,
        TaskInformation $information,
        UserId $ownerId,
        UserId $currentUserId
    ): Task {
        $status = new ActiveTaskStatus();
        $task = new Task(
            $id,
            $information,
            $ownerId,
            $status,
            new TaskLinkCollection()
        );

        $this->ensureCanChangeTask($task->getOwnerId(), $currentUserId);
        $this->ensureIsFinishDateGreaterThanTaskDates($information->startDate, $information->finishDate);

        if (!$this->isOwner($ownerId) && !$this->isParticipant($ownerId)) {
            throw new TaskUserNotExistException();
        }

        $task->registerEvent(new TaskWasCreatedEvent(
            $this->id->value,
            $this->projectId->value,
            $task->getId()->value,
            $information->name->value,
            $information->brief->value,
            $information->description->value,
            $information->startDate->getValue(),
            $information->finishDate->getValue(),
            $ownerId->value,
            (string)$status->getScalar()
        ));

        return $task;
    }

    public function changeTaskInformation(
        TaskId $taskId,
        TaskInformation $information,
        UserId $currentUserId
    ): void {
        $this->ensureTaskExists($taskId);

        /** @var Task $task */
        $task = $this->tasks->get($taskId->getHash());
        $this->ensureCanChangeTask($task->getOwnerId(), $currentUserId);
        $this->ensureIsFinishDateGreaterThanTaskDates($information->startDate, $information->finishDate);

        $task->changeInformation($information);

        $this->registerEvent(new TaskInformationWasChangedEvent(
            $this->id->value,
            $this->projectId->value,
            $task->getId()->value,
            $information->name->value,
            $information->brief->value,
            $information->description->value,
            $information->startDate->getValue(),
            $information->finishDate->getValue(),
        ));
    }

    public function changeTaskStatus(TaskId $taskId, TaskStatus $status, UserId $currentUserId): void
    {
        $this->ensureTaskExists($taskId);

        /** @var Task $task */
        $task = $this->tasks->get($taskId->getHash());
        $this->ensureCanChangeTask($task->getOwnerId(), $currentUserId);
        $task->changeStatus($status);

        $this->registerEvent(new TaskStatusWasChangedEvent(
            $this->id->value,
            $this->projectId->value,
            $task->getId()->value,
            (string) $status->getScalar()
        ));
    }

    public function createTaskLink(
        TaskId $fromTaskId,
        TaskId $toTaskId,
        UserId $currentUserId
    ): void {
        $this->ensureTaskExists($fromTaskId);
        $this->ensureTaskExists($toTaskId);

        /** @var Task $task */
        $task = $this->tasks->get($fromTaskId->getHash());
        $this->ensureCanChangeTask($task->getOwnerId(), $currentUserId);
        $task->addLink($toTaskId);

        $this->registerEvent(new TaskLinkWasAddedEvent(
            $this->id->value,
            $fromTaskId->value,
            $toTaskId->value
        ));
    }

    public function deleteTaskLink(
        TaskId $fromTaskId,
        TaskId $toTaskId,
        UserId $currentUserId
    ): void {
        $this->ensureTaskExists($fromTaskId);
        $this->ensureTaskExists($toTaskId);

        /** @var Task $task */
        $task = $this->tasks->get($fromTaskId->getHash());
        $this->ensureCanChangeTask($task->getOwnerId(), $currentUserId);
        $task->deleteLink($toTaskId);

        $this->registerEvent(new TaskLinkWasDeletedEvent(
            $this->id->value,
            $fromTaskId->value,
            $toTaskId->value
        ));
    }

    private function ensureCanChangeTask(UserId $taskOwnerId, UserId $currentUserId): void
    {
        $this->status->ensureAllowsModification();
        if (!$this->isOwner($currentUserId) && $taskOwnerId->isEqual($currentUserId)) {
            throw new InsufficientPermissionsToChangeTaskException();
        }
    }

    private function ensureIsFinishDateGreaterThanTaskDates(DateTime $startDate, DateTime $finishDate): void
    {
        if ($startDate->isGreaterThan($this->finishDate)) {
            throw new TaskStartDateGreaterThanProjectFinishDateException();
        }
        if ($finishDate->isGreaterThan($this->finishDate)) {
            throw new TaskFinishDateGreaterThanProjectFinishDateException();
        }
    }

    private function ensureTaskExists(TaskId $taskId): void
    {
        if (!$this->tasks->hashExists($taskId->getHash())) {
            throw new TaskNotExistException();
        }
    }

    private function isOwner(UserId $userId): bool
    {
        return $this->ownerId->isEqual($userId);
    }

    private function isParticipant(UserId $userId): bool
    {
        return $this->participantIds->hashExists($userId->getHash());
    }
}
