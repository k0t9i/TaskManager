<?php
declare(strict_types=1);

namespace App\Tasks\Application\Service;

use App\Shared\Domain\Bus\Event\DomainEvent;
use App\Shared\Domain\Event\ProjectInformationWasChangedEvent;
use App\Shared\Domain\Event\ProjectOwnerWasChangedEvent;
use App\Shared\Domain\Event\ProjectParticipantWasAddedEvent;
use App\Shared\Domain\Event\ProjectParticipantWasRemovedEvent;
use App\Shared\Domain\Event\ProjectStatusWasChangedEvent;
use App\Shared\Domain\Event\ProjectTaskFinishDateWasChangedEvent;
use App\Shared\Domain\Event\ProjectTaskStartDateWasChangedEvent;
use App\Shared\Domain\Event\ProjectTaskStatusWasChangedEvent;
use App\Shared\Domain\Exception\LogicException;
use App\Shared\Domain\ValueObject\TaskId;
use App\Shared\Domain\ValueObject\UserId;
use App\Tasks\Domain\DTO\TaskManagerMergeDTO;
use App\Tasks\Domain\DTO\TaskMergeDTO;
use App\Tasks\Domain\Entity\Task;
use App\Tasks\Domain\Entity\TaskManager;
use App\Tasks\Domain\Factory\TaskManagerMerger;
use App\Tasks\Domain\Factory\TaskMerger;

final class TaskManagerStateRecreator
{
    public function __construct(
        private readonly TaskManagerMerger $managerMerger,
        private readonly TaskMerger $taskMerger
    ) {
    }

    public function fromEvent(TaskManager $source, DomainEvent $event): TaskManager
    {
        if ($event instanceof ProjectStatusWasChangedEvent) {
            return $this->changeStatus($source, $event);
        }
        if ($event instanceof ProjectOwnerWasChangedEvent) {
            return $this->changeOwner($source, $event);
        }
        if ($event instanceof ProjectParticipantWasRemovedEvent) {
            return $this->removeParticipant($source, $event);
        }
        if ($event instanceof ProjectParticipantWasAddedEvent) {
            return $this->addParticipant($source, $event);
        }
        if ($event instanceof ProjectInformationWasChangedEvent) {
            return $this->changeFinishDate($source, $event);
        }
        if ($event instanceof ProjectTaskStartDateWasChangedEvent) {
            return $this->changeTaskStartDate($source, $event);
        }
        if ($event instanceof ProjectTaskFinishDateWasChangedEvent) {
            return $this->changeTaskFinishDate($source, $event);
        }
        if ($event instanceof ProjectTaskStatusWasChangedEvent) {
            return $this->changeTaskStatus($source, $event);
        }

        throw new LogicException(sprintf('Invalid domain event "%s"', get_class($event)));
    }

    private function changeStatus(TaskManager $source, ProjectStatusWasChangedEvent $event): TaskManager
    {
        return $this->managerMerger->merge($source, new TaskManagerMergeDTO(
            status: (int) $event->status
        ));
    }

    private function changeOwner(TaskManager $source, ProjectOwnerWasChangedEvent $event): TaskManager
    {
        return $this->managerMerger->merge($source, new TaskManagerMergeDTO(
            ownerId: $event->ownerId
        ));
    }

    private function removeParticipant(TaskManager $source, ProjectParticipantWasRemovedEvent $event): TaskManager
    {
        $participants = $source->getParticipants()->remove(new UserId($event->participantId));

        return $this->managerMerger->merge($source, new TaskManagerMergeDTO(
            participantIds: $participants->getInnerItems()
        ));
    }

    private function addParticipant(TaskManager $source, ProjectParticipantWasAddedEvent $event): TaskManager
    {
        $participants = $source->getParticipants()->add(new UserId($event->participantId));

        return $this->managerMerger->merge($source, new TaskManagerMergeDTO(
            participantIds: $participants->getInnerItems()
        ));
    }

    private function changeFinishDate(TaskManager $source, ProjectInformationWasChangedEvent $event): TaskManager
    {
        return $this->managerMerger->merge($source, new TaskManagerMergeDTO(
            finishDate: $event->finishDate
        ));
    }

    private function changeTaskStartDate(TaskManager $source, ProjectTaskStartDateWasChangedEvent $event): TaskManager
    {
        /** @var Task $task */
        $task = $source->getTasks()->get(new TaskId($event->taskId));
        //TODO throw exception?
        if ($task === null) {
            return $source;
        }

        $tasks = $source->getTasks()->add(
            $this->taskMerger->merge($task, new TaskMergeDTO(
                startDate: $event->startDate
            ))
        );

        return $this->managerMerger->merge($source, new TaskManagerMergeDTO(
            tasks: $tasks->getInnerItems()
        ));
    }

    private function changeTaskFinishDate(TaskManager $source, ProjectTaskFinishDateWasChangedEvent $event): TaskManager
    {
        /** @var Task $task */
        $task = $source->getTasks()->get(new TaskId($event->taskId));
        //TODO throw exception?
        if ($task === null) {
            return $source;
        }

        $tasks = $source->getTasks()->add(
            $this->taskMerger->merge($task, new TaskMergeDTO(
                finishDate: $event->finishDate
            ))
        );

        return $this->managerMerger->merge($source, new TaskManagerMergeDTO(
            tasks: $tasks->getInnerItems()
        ));
    }

    private function changeTaskStatus(TaskManager $source, ProjectTaskStatusWasChangedEvent $event): TaskManager
    {
        /** @var Task $task */
        $task = $source->getTasks()->get(new TaskId($event->taskId));
        //TODO throw exception?
        if ($task === null) {
            return $source;
        }

        $tasks = $source->getTasks()->add(
            $this->taskMerger->merge($task, new TaskMergeDTO(
                status: (int) $event->status
            ))
        );

        return $this->managerMerger->merge($source, new TaskManagerMergeDTO(
            tasks: $tasks->getInnerItems()
        ));
    }
}
