<?php
declare(strict_types=1);

namespace App\Tasks\Domain\Entity;

use App\Shared\Domain\Aggregate\AggregateRoot;
use App\Shared\Domain\Collection\Hashable;
use App\Shared\Domain\ValueObject\ActiveTaskStatus;
use App\Shared\Domain\ValueObject\ProjectId;
use App\Shared\Domain\ValueObject\TaskId;
use App\Shared\Domain\ValueObject\TaskStatus;
use App\Shared\Domain\ValueObject\UserId;
use App\Tasks\Domain\Collection\TaskLinkCollection;
use App\Tasks\Domain\Event\TaskInformationWasChangedEvent;
use App\Tasks\Domain\Event\TaskLinkWasAddedEvent;
use App\Tasks\Domain\Event\TaskLinkWasDeletedEvent;
use App\Tasks\Domain\Event\TaskStatusWasChangedEvent;
use App\Tasks\Domain\Event\TaskWasCreatedEvent;
use App\Tasks\Domain\Exception\TaskLinkAlreadyExistsException;
use App\Tasks\Domain\Exception\TaskLinkNotExistException;
use App\Tasks\Domain\Exception\TaskStartDateGreaterThanProjectFinishDateException;
use App\Tasks\Domain\Exception\TaskUserNotExistException;
use App\Tasks\Domain\ValueObject\TaskInformation;
use App\Tasks\Domain\ValueObject\TaskLink;

final class Task extends AggregateRoot implements Hashable
{
    public function __construct(
        private TaskId $id,
        private ProjectId $projectId,
        private TaskInformation $information,
        private UserId $ownerId,
        private TaskStatus $status,
        private TaskProject $taskProject,
        private TaskLinkCollection $links
    ) {
        $this->ensureFinishDateGreaterThanStart();
    }

    public static function create(
        TaskId $id,
        ProjectId $projectId,
        TaskInformation $information,
        UserId $ownerId,
        TaskProject $taskProject,
        UserId $currentUserId
    ): self {
        $status = new ActiveTaskStatus();
        $task = new Task(
            $id,
            $projectId,
            $information,
            $ownerId,
            $status,
            $taskProject,
            new TaskLinkCollection()
        );

        $task->taskProject->ensureCanChangeTask($ownerId, $currentUserId);
        $task->taskProject->ensureIsFinishDateGreaterThanTaskDates($information->startDate, $information->finishDate);

        if (!$task->taskProject->isUserInProject($ownerId)) {
            throw new TaskUserNotExistException();
        }

        //TODO send task project info
        $task->registerEvent(new TaskWasCreatedEvent(
            $id->value,
            $task->projectId->value,
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

    public function changeInformation(
        TaskInformation $information,
        UserId $currentUserId
    ): void {
        $this->taskProject->ensureCanChangeTask($this->ownerId, $currentUserId);
        $this->taskProject->ensureIsFinishDateGreaterThanTaskDates($information->startDate, $information->finishDate);

        $this->status->ensureAllowsModification();
        $this->information = $information;
        $this->ensureFinishDateGreaterThanStart();

        $this->registerEvent(new TaskInformationWasChangedEvent(
            $this->projectId->value,
            $this->id->value,
            $information->name->value,
            $information->brief->value,
            $information->description->value,
            $information->startDate->getValue(),
            $information->finishDate->getValue(),
        ));
    }

    public function changeStatus(TaskStatus $status, UserId $currentUserId): void
    {
        $this->taskProject->ensureCanChangeTask($this->ownerId, $currentUserId);

        $this->status->ensureCanBeChangedTo($status);
        $this->status = $status;

        $this->registerEvent(new TaskStatusWasChangedEvent(
            $this->projectId->value,
            $this->id->value,
            (string) $status->getScalar()
        ));
    }

    public function createLink(
        TaskId $toTaskId,
        UserId $currentUserId
    ): void {
        $this->taskProject->ensureCanChangeTask($this->ownerId, $currentUserId);
        $this->taskProject->ensureTaskExists($toTaskId);

        $taskLink = new TaskLink($this->id, $toTaskId);
        $this->ensureLinkDoesNotExist($taskLink);

        $this->links->add($taskLink);

        $this->registerEvent(new TaskLinkWasAddedEvent(
            $this->id->value,
            $toTaskId->value
        ));
    }

    public function deleteLink(
        TaskId $toTaskId,
        UserId             $currentUserId
    ): void {
        $this->taskProject->ensureCanChangeTask($this->ownerId, $currentUserId);
        $this->taskProject->ensureTaskExists($toTaskId);

        $taskLink = new TaskLink($this->id, $toTaskId);
        $this->ensureLinkExists($taskLink);

        $this->links->remove($taskLink);

        $this->registerEvent(new TaskLinkWasDeletedEvent(
            $this->id->value,
            $toTaskId->value
        ));
    }

    /**
     * @return TaskId
     */
    public function getId(): TaskId
    {
        return $this->id;
    }

    /**
     * @return TaskInformation
     */
    public function getInformation(): TaskInformation
    {
        return $this->information;
    }

    public function getHash(): string
    {
        return $this->id->getHash();
    }

    private function ensureFinishDateGreaterThanStart()
    {
        if ($this->information->startDate->isGreaterThan($this->information->finishDate)) {
            throw new TaskStartDateGreaterThanProjectFinishDateException();
        }
    }

    private function ensureLinkDoesNotExist(TaskLink $link): void
    {
        if ($this->links->exists($link)) {
            throw new TaskLinkAlreadyExistsException();
        }
    }

    private function ensureLinkExists(TaskLink $link): void
    {
        if (!$this->links->exists($link)) {
            throw new TaskLinkNotExistException();
        }
    }
}
