<?php
declare(strict_types=1);

namespace App\Tasks\Domain\Entity;

use App\Projects\Domain\Entity\Project;
use App\Shared\Domain\Collection\Hashable;
use App\Shared\Domain\ValueObject\UserId;
use App\Tasks\Domain\Event\TaskFinishDateWasChangedEvent;
use App\Tasks\Domain\Event\TaskStartDateWasChangedEvent;
use App\Tasks\Domain\Event\TaskStatusWasChangedEvent;
use App\Tasks\Domain\Exception\TaskStartDateGreaterThanFinishDateException;
use App\Tasks\Domain\Factory\TaskStatusFactory;
use App\Tasks\Domain\ValueObject\ActiveTaskStatus;
use App\Tasks\Domain\ValueObject\ClosedTaskStatus;
use App\Tasks\Domain\ValueObject\TaskBrief;
use App\Tasks\Domain\ValueObject\TaskDescription;
use App\Tasks\Domain\ValueObject\TaskFinishDate;
use App\Tasks\Domain\ValueObject\TaskId;
use App\Tasks\Domain\ValueObject\TaskName;
use App\Tasks\Domain\ValueObject\TaskStartDate;
use App\Tasks\Domain\ValueObject\TaskStatus;
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
        private User $owner,
        private TaskStatus $status,
        private Project $project
    ) {
        $this->ensureFinishDateGreaterThanStart();
    }

    /**
     * @return Project
     */
    public function getProject(): Project
    {
        return $this->project;
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
    public function getOwner(): User
    {
        return $this->owner;
    }

    /**
     * @return TaskStatus
     */
    public function getStatus(): TaskStatus
    {
        return $this->status;
    }

    /**
     * @param TaskName $name
     */
    private function setName(TaskName $name): void
    {
        $this->name = $name;
    }

    /**
     * @param TaskBrief $brief
     */
    private function setBrief(TaskBrief $brief): void
    {
        $this->brief = $brief;
    }

    /**
     * @param TaskDescription $description
     */
    private function setDescription(TaskDescription $description): void
    {
        $this->description = $description;
    }

    /**
     * @param TaskStartDate $startDate
     */
    private function setStartDate(TaskStartDate $startDate): void
    {
        $this->startDate = $startDate;
    }

    /**
     * @param TaskFinishDate $finishDate
     */
    private function setFinishDate(TaskFinishDate $finishDate): void
    {
        $this->finishDate = $finishDate;
    }

    /**
     * @param TaskStatus $status
     */
    private function setStatus(TaskStatus $status): void
    {
        $this->status = $status;
    }

    public function isOwner(UserId $userId): bool
    {
        return $this->owner->getId()->isEqual($userId);
    }

    public function limitDatesByProjectFinishDate(Project $project): void
    {
        if ($this->getStartDate()->isGreaterThan($project->getFinishDate())) {
            $this->setStartDate(new TaskStartDate($project->getFinishDate()->getValue()));
            $project->registerEvent(new TaskStartDateWasChangedEvent(
                $this->getId()->value,
                $this->getStartDate()->getValue()
            ));
        }
        if ($this->getFinishDate()->isGreaterThan($project->getFinishDate())) {
            $this->setFinishDate(new TaskFinishDate($project->getFinishDate()->getValue()));
            $project->registerEvent(new TaskFinishDateWasChangedEvent(
                $this->getId()->value,
                $this->getFinishDate()->getValue()
            ));
        }
    }

    public function changeInformation(
        TaskName $name,
        TaskBrief $brief,
        TaskDescription $description,
        TaskStartDate $startDate,
        TaskFinishDate $finishDate,
    ): void {
        $this->getStatus()->ensureAllowsModification();
        $this->setName($name);
        $this->setBrief($brief);
        $this->setDescription($description);
        $this->setStartDate($startDate);
        $this->setFinishDate($finishDate);
    }

    public function closeTaskIfProjectWasClosed(Project $project): void
    {
        if ($this->getStatus() instanceof ActiveTaskStatus) {
            $this->setStatus(new ClosedTaskStatus());
            $project->registerEvent(new TaskStatusWasChangedEvent(
                $this->getId()->value,
                TaskStatusFactory::scalarFromObject($this->getStatus())
            ));
        }
    }

    public function changeStatus(TaskStatus $status): void
    {
        $this->getStatus()->ensureCanBeChangedTo($status);
        $this->setStatus($status);
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
