<?php
declare(strict_types=1);

namespace App\ProjectTasks\Domain\Entity;

use App\Projects\Domain\Entity\Project;
use App\ProjectTasks\Domain\Event\TaskFinishDateWasChangedEvent;
use App\ProjectTasks\Domain\Event\TaskStartDateWasChangedEvent;
use App\ProjectTasks\Domain\Event\TaskStatusWasChangedEvent;
use App\ProjectTasks\Domain\Exception\TaskStartDateGreaterThanFinishDateException;
use App\ProjectTasks\Domain\Factory\TaskStatusFactory;
use App\ProjectTasks\Domain\ValueObject\ActiveTaskStatus;
use App\ProjectTasks\Domain\ValueObject\ClosedTaskStatus;
use App\ProjectTasks\Domain\ValueObject\TaskBrief;
use App\ProjectTasks\Domain\ValueObject\TaskDescription;
use App\ProjectTasks\Domain\ValueObject\TaskFinishDate;
use App\ProjectTasks\Domain\ValueObject\TaskId;
use App\ProjectTasks\Domain\ValueObject\TaskInformation;
use App\ProjectTasks\Domain\ValueObject\TaskName;
use App\ProjectTasks\Domain\ValueObject\TaskStartDate;
use App\ProjectTasks\Domain\ValueObject\TaskStatus;
use App\Shared\Domain\Collection\Hashable;
use App\Shared\Domain\ValueObject\UserId;
use App\Users\Domain\Entity\User;

class Task implements Hashable
{
    public function __construct(
        private TaskId $id,
        private TaskName $name,
        private TaskBrief $brief,
        private TaskDescription $description,
        private TaskStartDate $startDate,
        private TaskFinishDate $finishDate,
        private UserId $ownerId,
        private TaskStatus $status
    ) {
        $this->ensureFinishDateGreaterThanStart();
    }

    /**
     * @return TaskId
     */
    public function getId(): TaskId
    {
        return $this->id;
    }

    /**
     * @return TaskName
     */
    public function getName(): TaskName
    {
        return $this->name;
    }

    /**
     * @return TaskBrief
     */
    public function getBrief(): TaskBrief
    {
        return $this->brief;
    }

    /**
     * @return TaskDescription
     */
    public function getDescription(): TaskDescription
    {
        return $this->description;
    }

    /**
     * @return TaskStartDate
     */
    public function getStartDate(): TaskStartDate
    {
        return $this->startDate;
    }

    /**
     * @return TaskFinishDate
     */
    public function getFinishDate(): TaskFinishDate
    {
        return $this->finishDate;
    }

    /**
     * @return User
     */
    public function getOwnerId(): UserId
    {
        return $this->ownerId;
    }

    /**
     * @return TaskStatus
     */
    public function getStatus(): TaskStatus
    {
        return $this->status;
    }

    public function isOwner(UserId $userId): bool
    {
        return $this->ownerId->isEqual($userId);
    }

    public function limitDatesByProjectFinishDate(Project $project): void
    {
        if ($this->getStartDate()->isGreaterThan($project->getFinishDate())) {
            $this->startDate = new TaskStartDate($project->getFinishDate()->getValue());
            $project->registerEvent(new TaskStartDateWasChangedEvent(
                $this->getId()->value,
                $this->getStartDate()->getValue()
            ));
        }
        if ($this->getFinishDate()->isGreaterThan($project->getFinishDate())) {
            $this->finishDate = new TaskFinishDate($project->getFinishDate()->getValue());
            $project->registerEvent(new TaskFinishDateWasChangedEvent(
                $this->getId()->value,
                $this->getFinishDate()->getValue()
            ));
        }
    }

    public function changeInformation(TaskInformation $information): void {
        $this->getStatus()->ensureAllowsModification();
        $this->name = $information->name;
        $this->brief = $information->brief;
        $this->description = $information->description;
        $this->startDate = $information->startDate;
        $this->finishDate = $information->finishDate;
        $this->ensureFinishDateGreaterThanStart();
    }

    public function closeTaskIfProjectWasClosed(Project $project): void
    {
        if ($this->getStatus() instanceof ActiveTaskStatus) {
            $this->status = new ClosedTaskStatus();
            $project->registerEvent(new TaskStatusWasChangedEvent(
                $this->getId()->value,
                TaskStatusFactory::scalarFromObject($this->getStatus())
            ));
        }
    }

    public function changeStatus(TaskStatus $status): void
    {
        $this->getStatus()->ensureCanBeChangedTo($status);
        $this->status = $status;
    }

    private function ensureFinishDateGreaterThanStart()
    {
        if ($this->startDate->isGreaterThan($this->finishDate)) {
            throw new TaskStartDateGreaterThanFinishDateException();
        }
    }

    public function getHash(): string
    {
        return $this->getId()->getHash();
    }
}
