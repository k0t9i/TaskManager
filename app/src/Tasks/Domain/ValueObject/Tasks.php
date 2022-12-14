<?php

declare(strict_types=1);

namespace App\Tasks\Domain\ValueObject;

use App\Shared\Domain\Exception\TaskNotExistException;
use App\Shared\Domain\ValueObject\DateTime;
use App\Shared\Domain\ValueObject\Tasks\TaskId;
use App\Tasks\Domain\Collection\TaskCollection;
use App\Tasks\Domain\Entity\Task;
use App\Tasks\Domain\Exception\TaskFinishDateGreaterThanProjectFinishDateException;
use App\Tasks\Domain\Exception\TaskStartDateGreaterThanProjectFinishDateException;

final class Tasks
{
    private TaskCollection $tasks;

    public function __construct(?TaskCollection $items = null)
    {
        if (null === $items) {
            $this->tasks = new TaskCollection();
        } else {
            $this->tasks = $items;
        }
    }

    public function add(Task $task): self
    {
        $result = new self();
        $result->tasks = $this->tasks->add($task);

        return $result;
    }

    public function ensureIsFinishDateGreaterThanTaskDates(TaskId $taskId, DateTime $date): void
    {
        /** @var Task $task */
        $task = $this->tasks->get($taskId->getHash());
        if (null === $task) {
            return;
        }

        $startDate = $task->getInformation()->startDate;
        if ($startDate->isGreaterThan($date)) {
            throw new TaskStartDateGreaterThanProjectFinishDateException($date->getValue(), $startDate->getValue());
        }
        $finishDate = $task->getInformation()->finishDate;
        if ($finishDate->isGreaterThan($date)) {
            throw new TaskFinishDateGreaterThanProjectFinishDateException($date->getValue(), $finishDate->getValue());
        }
    }

    public function ensureTaskExists(TaskId $taskId): void
    {
        if (!$this->tasks->hashExists($taskId->getHash())) {
            throw new TaskNotExistException($taskId->value);
        }
    }

    public function getCollection(): TaskCollection
    {
        return $this->tasks;
    }
}
