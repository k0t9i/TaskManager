<?php
declare(strict_types=1);

namespace App\TaskManagers\Domain\Entity;

use App\Shared\Domain\Collection\Hashable;
use App\Shared\Domain\ValueObject\DateTime;
use App\Shared\Domain\ValueObject\UserId;
use App\TaskManagers\Domain\Exception\TaskStartDateGreaterThanFinishDateException;
use App\TaskManagers\Domain\ValueObject\TaskBrief;
use App\TaskManagers\Domain\ValueObject\TaskDescription;
use App\TaskManagers\Domain\ValueObject\TaskId;
use App\TaskManagers\Domain\ValueObject\TaskInformation;
use App\TaskManagers\Domain\ValueObject\TaskName;
use App\TaskManagers\Domain\ValueObject\TaskStatus;

class Task implements Hashable
{
    public function __construct(
        private TaskId $id,
        private TaskName $name,
        private TaskBrief $brief,
        private TaskDescription $description,
        private DateTime $startDate,
        private DateTime $finishDate,
        private UserId $ownerId,
        private TaskStatus $status
    ) {
        $this->ensureFinishDateGreaterThanStart();
    }

    public function getId(): TaskId
    {
        return $this->id;
    }

    public function getName(): TaskName
    {
        return $this->name;
    }

    public function getBrief(): TaskBrief
    {
        return $this->brief;
    }

    public function getDescription(): TaskDescription
    {
        return $this->description;
    }

    public function getStartDate(): DateTime
    {
        return $this->startDate;
    }

    public function getFinishDate(): DateTime
    {
        return $this->finishDate;
    }

    public function getOwnerId(): UserId
    {
        return $this->ownerId;
    }

    public function getStatus(): TaskStatus
    {
        return $this->status;
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
