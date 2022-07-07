<?php
declare(strict_types=1);

namespace App\Tasks\Domain\Entity;

use App\Shared\Domain\Aggregate\AggregateRoot;
use App\Shared\Domain\Event\TaskInformationWasChangedEvent;
use App\Shared\Domain\Event\TaskStatusWasChangedEvent;
use App\Shared\Domain\Event\TaskWasCreatedEvent;
use App\Shared\Domain\ValueObject\ActiveTaskStatus;
use App\Shared\Domain\ValueObject\DateTime;
use App\Shared\Domain\ValueObject\Owner;
use App\Shared\Domain\ValueObject\Participants;
use App\Shared\Domain\ValueObject\ProjectId;
use App\Shared\Domain\ValueObject\ProjectStatus;
use App\Shared\Domain\ValueObject\TaskId;
use App\Shared\Domain\ValueObject\TaskStatus;
use App\Shared\Domain\ValueObject\UserId;
use App\Tasks\Domain\Collection\TaskCollection;
use App\Tasks\Domain\Collection\TaskLinkCollection;
use App\Tasks\Domain\Event\TaskLinkWasAddedEvent;
use App\Tasks\Domain\Event\TaskLinkWasDeletedEvent;
use App\Tasks\Domain\Exception\InsufficientPermissionsToChangeTaskException;
use App\Tasks\Domain\Exception\TaskUserNotExistException;
use App\Tasks\Domain\ValueObject\TaskInformation;
use App\Tasks\Domain\ValueObject\TaskManagerId;
use App\Tasks\Domain\ValueObject\Tasks;

final class TaskManager extends AggregateRoot
{
    public function __construct(
        private TaskManagerId  $id,
        private ProjectId      $projectId,
        private ProjectStatus  $status,
        private Owner          $owner,
        private DateTime       $finishDate,
        private Participants   $participants,
        private Tasks          $tasks
    ) {
    }

    public function createTask(
        TaskId          $id,
        TaskInformation $information,
        UserId          $ownerId,
        UserId          $currentUserId
    ): Task {
        $this->status->ensureAllowsModification();

        $status = new ActiveTaskStatus();
        $task = new Task(
            $id,
            $information,
            $ownerId,
            $status,
            new TaskLinkCollection()
        );

        $this->ensureCanChangeTask($task->getOwnerId(), $currentUserId);
        $this->tasks->ensureIsFinishDateGreaterThanTaskDates($task->getId(), $this->finishDate);

        if (!$this->owner->isOwner($ownerId) && !$this->participants->isParticipant($ownerId)) {
            throw new TaskUserNotExistException($ownerId->value);
        }

        $this->tasks = $this->tasks->add($task);

        $this->registerEvent(new TaskWasCreatedEvent(
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
        $this->status->ensureAllowsModification();
        $this->tasks->ensureTaskExists($taskId);

        /** @var Task $task */
        $task = $this->tasks->get($taskId);
        $this->ensureCanChangeTask($task->getOwnerId(), $currentUserId);
        $this->tasks->ensureIsFinishDateGreaterThanTaskDates($task->getId(), $this->finishDate);

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
        $this->status->ensureAllowsModification();
        $this->tasks->ensureTaskExists($taskId);

        /** @var Task $task */
        $task = $this->tasks->get($taskId);
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
        $this->status->ensureAllowsModification();
        $this->tasks->ensureTaskExists($fromTaskId);
        $this->tasks->ensureTaskExists($toTaskId);

        /** @var Task $task */
        $task = $this->tasks->get($fromTaskId);
        $this->ensureCanChangeTask($task->getOwnerId(), $currentUserId);
        $task->addLink($toTaskId);

        /** @var Task $task */
        $task = $this->tasks->get($toTaskId);
        $task->addLink($fromTaskId);

        $this->registerEvent(new TaskLinkWasAddedEvent(
            $this->id->value,
            $fromTaskId->value,
            $toTaskId->value
        ));
        $this->registerEvent(new TaskLinkWasAddedEvent(
            $this->id->value,
            $toTaskId->value,
            $fromTaskId->value
        ));
    }

    public function deleteTaskLink(
        TaskId $fromTaskId,
        TaskId $toTaskId,
        UserId $currentUserId
    ): void {
        $this->status->ensureAllowsModification();
        $this->tasks->ensureTaskExists($fromTaskId);
        $this->tasks->ensureTaskExists($toTaskId);

        /** @var Task $task */
        $task = $this->tasks->get($fromTaskId);
        $this->ensureCanChangeTask($task->getOwnerId(), $currentUserId);
        $task->deleteLink($toTaskId);

        /** @var Task $task */
        $task = $this->tasks->get($toTaskId);
        $task->deleteLink($fromTaskId);

        $this->registerEvent(new TaskLinkWasDeletedEvent(
            $this->id->value,
            $fromTaskId->value,
            $toTaskId->value
        ));
        $this->registerEvent(new TaskLinkWasDeletedEvent(
            $this->id->value,
            $toTaskId->value,
            $fromTaskId->value
        ));
    }

    public function getId(): TaskManagerId
    {
        return $this->id;
    }

    public function getProjectId(): ProjectId
    {
        return $this->projectId;
    }

    public function getStatus(): ProjectStatus
    {
        return $this->status;
    }

    public function getOwner(): Owner
    {
        return $this->owner;
    }

    public function getFinishDate(): DateTime
    {
        return $this->finishDate;
    }

    public function getParticipants(): Participants
    {
        return $this->participants;
    }

    public function getTasks(): Tasks
    {
        return $this->tasks;
    }

    public function getTasksForProjectUser(UserId $userId): TaskCollection
    {
        if (!$this->owner->isOwner($userId) && !$this->participants->isParticipant($userId)) {
            throw new TaskUserNotExistException($userId->value);
        }
        return $this->tasks->getInnerItems();
    }

    private function ensureCanChangeTask(UserId $taskOwnerId, UserId $currentUserId): void
    {
        if (!$this->owner->isOwner($currentUserId) && !$taskOwnerId->isEqual($currentUserId)) {
            throw new InsufficientPermissionsToChangeTaskException();
        }
    }
}
