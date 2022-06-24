<?php
declare(strict_types=1);

namespace App\Projects\Domain\Entity;

//TODO think about using of ProjectTasks namespace
use App\ProjectTasks\Domain\Event\TaskFinishDateWasChangedEvent;
use App\ProjectTasks\Domain\Event\TaskStartDateWasChangedEvent;
use App\ProjectTasks\Domain\Event\TaskStatusWasChangedEvent;
use App\ProjectTasks\Domain\ValueObject\ActiveTaskStatus;
use App\ProjectTasks\Domain\ValueObject\ClosedTaskStatus;
use App\ProjectTasks\Domain\ValueObject\TaskId;
use App\ProjectTasks\Domain\ValueObject\TaskStatus;
use App\Shared\Domain\Collection\Hashable;
use App\Shared\Domain\ValueObject\DateTime;

final class ProjectTask implements Hashable
{
    public function __construct(
        private TaskId $id,
        private TaskStatus $status,
        private DateTime $startDate,
        private DateTime $finishDate
    ) {
    }

    public function limitDatesByProjectFinishDate(Project $project): void
    {
        if ($this->getStartDate()->isGreaterThan($project->getFinishDate())) {
            $this->startDate = new DateTime($project->getFinishDate()->getValue());
            $project->registerEvent(new TaskStartDateWasChangedEvent(
                $this->getId()->value,
                $this->getStartDate()->getValue()
            ));
        }
        if ($this->getFinishDate()->isGreaterThan($project->getFinishDate())) {
            $this->finishDate = new DateTime($project->getFinishDate()->getValue());
            $project->registerEvent(new TaskFinishDateWasChangedEvent(
                $this->getId()->value,
                $this->getFinishDate()->getValue()
            ));
        }
    }

    public function closeTaskIfProjectWasClosed(Project $project): void
    {
        if ($this->getStatus() instanceof ActiveTaskStatus) {
            $this->status = new ClosedTaskStatus();
            $project->registerEvent(new TaskStatusWasChangedEvent(
                $this->getId()->value,
                $this->getStatus()->getScalar()
            ));
        }
    }

    public function getId(): TaskId
    {
        return $this->id;
    }

    public function getStatus(): TaskStatus
    {
        return $this->status;
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
