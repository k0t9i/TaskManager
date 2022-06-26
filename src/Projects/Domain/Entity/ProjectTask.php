<?php
declare(strict_types=1);

namespace App\Projects\Domain\Entity;

//TODO think about using of ProjectTasks namespace
use App\Projects\Domain\Event\ProjectTaskFinishDateWasChangedEvent;
use App\Projects\Domain\Event\ProjectTaskStartDateWasChangedEvent;
use App\Projects\Domain\Event\ProjectTaskStatusWasChangedEvent;
use App\Projects\Domain\ValueObject\ProjectTaskId;
use App\Shared\Domain\Collection\Hashable;
use App\Shared\Domain\ValueObject\ActiveTaskStatus;
use App\Shared\Domain\ValueObject\ClosedTaskStatus;
use App\Shared\Domain\ValueObject\DateTime;
use App\Shared\Domain\ValueObject\TaskStatus;

final class ProjectTask implements Hashable
{
    public function __construct(
        private ProjectTaskId $id, //TODO same as TaskId
        private TaskStatus $status,
        private DateTime $startDate,
        private DateTime $finishDate
    ) {
    }

    public function limitDatesByProjectFinishDate(Project $project): void
    {
        if ($this->getStartDate()->isGreaterThan($project->getFinishDate())) {
            $this->startDate = new DateTime($project->getFinishDate()->getValue());
            $project->registerEvent(new ProjectTaskStartDateWasChangedEvent(
                $project->getId()->value,
                $this->getId()->value,
                $this->getStartDate()->getValue()
            ));
        }
        if ($this->getFinishDate()->isGreaterThan($project->getFinishDate())) {
            $this->finishDate = new DateTime($project->getFinishDate()->getValue());
            $project->registerEvent(new ProjectTaskFinishDateWasChangedEvent(
                $project->getId()->value,
                $this->getId()->value,
                $this->getFinishDate()->getValue()
            ));
        }
    }

    public function closeTaskIfProjectWasClosed(Project $project): void
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
