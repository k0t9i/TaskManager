<?php
declare(strict_types=1);

namespace App\Projects\Domain\Entity;

use App\Projects\Domain\Event\ProjectTaskFinishDateWasChangedEvent;
use App\Projects\Domain\Event\ProjectTaskStartDateWasChangedEvent;
use App\Projects\Domain\Event\ProjectTaskStatusWasChangedEvent;
use App\Projects\Domain\ValueObject\ProjectTaskId;
use App\Shared\Domain\Collection\Hashable;
use App\Shared\Domain\ValueObject\ActiveTaskStatus;
use App\Shared\Domain\ValueObject\ClosedTaskStatus;
use App\Shared\Domain\ValueObject\DateTime;
use App\Shared\Domain\ValueObject\TaskId;
use App\Shared\Domain\ValueObject\TaskStatus;
use App\Shared\Domain\ValueObject\UserId;

final class ProjectTask implements Hashable
{
    // TODO add task
    // TODO delete task
    // TODO change task status
    // TODO change task information
    public function __construct(
        private ProjectTaskId $id,
        private TaskId $taskId,
        private TaskStatus $status,
        private UserId $ownerId,
        private DateTime $startDate,
        private DateTime $finishDate
    ) {
    }

    public function limitDatesByProjectFinishDate(Project $project): void
    {
        $information = $project->getInformation();
        if ($this->getStartDate()->isGreaterThan($information->finishDate)) {
            $this->startDate = new DateTime($information->finishDate->getValue());
            $project->registerEvent(new ProjectTaskStartDateWasChangedEvent(
                $project->getId()->value,
                $this->getId()->value,
                $this->getStartDate()->getValue()
            ));
        }
        if ($this->getFinishDate()->isGreaterThan($information->finishDate)) {
            $this->finishDate = new DateTime($information->finishDate->getValue());
            $project->registerEvent(new ProjectTaskFinishDateWasChangedEvent(
                $project->getId()->value,
                $this->getId()->value,
                $this->getFinishDate()->getValue()
            ));
        }
    }

    public function closeIfActive(Project $project): void
    {
        if ($this->getStatus() instanceof ActiveTaskStatus) {
            $this->status = new ClosedTaskStatus();
            $project->registerEvent(new ProjectTaskStatusWasChangedEvent(
                $project->getId()->value,
                $this->getId()->value,
                (string) $this->getStatus()->getScalar()
            ));
        }
    }

    public function getId(): ProjectTaskId
    {
        return $this->id;
    }

    public function getTaskId(): TaskId
    {
        return $this->taskId;
    }

    public function getStatus(): TaskStatus
    {
        return $this->status;
    }

    public function getOwnerId(): UserId
    {
        return $this->ownerId;
    }

    public function getStartDate(): DateTime
    {
        return $this->startDate;
    }

    public function getFinishDate(): DateTime
    {
        return $this->finishDate;
    }

    public function getHash(): string
    {
        return $this->getId()->getHash();
    }
}
