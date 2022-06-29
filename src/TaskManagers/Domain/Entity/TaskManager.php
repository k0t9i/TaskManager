<?php
declare(strict_types=1);

namespace App\TaskManagers\Domain\Entity;

use App\Shared\Domain\Aggregate\AggregateRoot;
use App\Shared\Domain\Collection\UserIdCollection;
use App\Shared\Domain\ValueObject\ActiveTaskStatus;
use App\Shared\Domain\ValueObject\DateTime;
use App\Shared\Domain\ValueObject\ProjectStatus;
use App\Shared\Domain\ValueObject\TaskId;
use App\Shared\Domain\ValueObject\TaskStatus;
use App\Shared\Domain\ValueObject\UserId;
use App\TaskManagers\Domain\Collection\TaskCollection;
use App\TaskManagers\Domain\Event\TaskInformationWasChangedEvent;
use App\TaskManagers\Domain\Event\TaskStatusWasChangedEvent;
use App\TaskManagers\Domain\Event\TaskWasCreatedEvent;
use App\TaskManagers\Domain\Event\TaskWasDeletedEvent;
use App\TaskManagers\Domain\Exception\InsufficientPermissionsToChangeTaskManagerTaskException;
use App\TaskManagers\Domain\Exception\TaskFinishDateGreaterThanTaskManagerFinishDateException;
use App\TaskManagers\Domain\Exception\TaskManagerTaskNotExistException;
use App\TaskManagers\Domain\Exception\TaskManagerUserNotExistException;
use App\TaskManagers\Domain\Exception\TaskStartDateGreaterThanTaskManagerFinishDateException;
use App\TaskManagers\Domain\ValueObject\TaskInformation;
use App\TaskManagers\Domain\ValueObject\TaskManagerId;

final class TaskManager extends AggregateRoot
{
    public function __construct(
        private TaskManagerId    $id, //TODO same as ProjectId
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
    ): void {
        $this->getStatus()->ensureAllowsModification();
        $this->ensureCanChangeTask($ownerId, $currentUserId);

        if ($information->startDate->isGreaterThan($this->finishDate)) {
            throw new TaskStartDateGreaterThanTaskManagerFinishDateException();
        }
        if ($information->finishDate->isGreaterThan($this->finishDate)) {
            throw new TaskFinishDateGreaterThanTaskManagerFinishDateException();
        }
        if (!$this->isUserInProject($ownerId)) {
            throw new TaskManagerUserNotExistException();
        }

        $status = new ActiveTaskStatus();
        $task = new Task(
            $id,
            $information->name,
            $information->brief,
            $information->description,
            $information->startDate,
            $information->finishDate,
            $ownerId,
            $status
        );
        $this->tasks->add($task);

        $this->registerEvent(new TaskWasCreatedEvent(
            $this->id->value,
            $id->value,
            $information->name->value,
            $information->brief->value,
            $information->description->value,
            $information->startDate->getValue(),
            $information->finishDate->getValue(),
            $ownerId->value,
            (string) $status->getScalar(),
        ));
    }

    public function changeTaskInformation(
        TaskId $id,
        TaskInformation $information,
        UserId $currentUserId
    ): void {
        $this->getStatus()->ensureAllowsModification();

        if ($information->startDate->isGreaterThan($this->getFinishDate())) {
            throw new TaskStartDateGreaterThanTaskManagerFinishDateException();
        }
        if ($information->finishDate->isGreaterThan($this->getFinishDate())) {
            throw new TaskFinishDateGreaterThanTaskManagerFinishDateException();
        }
        $this->ensureProjectTaskExits($id);
        /** @var Task $task */
        $task = $this->tasks->get($id->getHash());
        $this->ensureCanChangeTask($task->getOwnerId(), $currentUserId);

        $task->changeInformation($information);

        $this->registerEvent(new TaskInformationWasChangedEvent(
            $this->id->value,
            $id->value,
            $information->name->value,
            $information->brief->value,
            $information->description->value,
            $information->startDate->getValue(),
            $information->finishDate->getValue(),
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
            $this->getId()->value,
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
            $this->getId()->value,
            $task->getId()->value,
            (string) $status->getScalar()
        ));
    }

    public function getId(): TaskManagerId
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

    public function getFinishDate(): DateTime
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
            throw new InsufficientPermissionsToChangeTaskManagerTaskException();
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
            throw new TaskManagerTaskNotExistException();
        }
    }
}
