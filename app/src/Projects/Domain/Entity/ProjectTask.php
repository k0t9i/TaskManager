<?php
declare(strict_types=1);

namespace App\Projects\Domain\Entity;

use App\Projects\Domain\ValueObject\ProjectTaskId;
use App\Shared\Domain\Collection\Hashable;
use App\Shared\Domain\Event\ProjectTaskFinishDateWasChangedEvent;
use App\Shared\Domain\Event\ProjectTaskStartDateWasChangedEvent;
use App\Shared\Domain\Event\ProjectTaskStatusWasChangedEvent;
use App\Shared\Domain\ValueObject\ActiveTaskStatus;
use App\Shared\Domain\ValueObject\ClosedTaskStatus;
use App\Shared\Domain\ValueObject\DateTime;
use App\Shared\Domain\ValueObject\TaskId;
use App\Shared\Domain\ValueObject\TaskStatus;
use App\Shared\Domain\ValueObject\UserId;

final class ProjectTask implements Hashable
{
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
                $this->getTaskId()->value,
                $this->getStartDate()->getValue()
            ));
        }
        if ($this->getFinishDate()->isGreaterThan($information->finishDate)) {
            $this->finishDate = new DateTime($information->finishDate->getValue());
            $project->registerEvent(new ProjectTaskFinishDateWasChangedEvent(
                $project->getId()->value,
                $this->getId()->value,
                $this->getTaskId()->value,
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
                $this->getTaskId()->value,
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

    /**
     * @param self $other
     * @return bool
     */
    public function isEqual(object $other): bool
    {
        if (get_class($this) !== get_class($other)) {
            return false;
        }
        return $this->id->isEqual($other->id) &&
            $this->taskId->isEqual($other->taskId) &&
            $this->status->isEqual($other->status) &&
            $this->ownerId->isEqual($other->ownerId) &&
            $this->startDate->isEqual($other->startDate) &&
            $this->finishDate->isEqual($other->finishDate);
    }
}
