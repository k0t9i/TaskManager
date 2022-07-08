<?php
declare(strict_types=1);

namespace App\Projects\Application\Service;

use App\Projects\Domain\DTO\ProjectMergeDTO;
use App\Projects\Domain\DTO\ProjectTaskDTO;
use App\Projects\Domain\DTO\ProjectTaskMergeDTO;
use App\Projects\Domain\Entity\Project;
use App\Projects\Domain\Factory\ProjectMerger;
use App\Projects\Domain\Factory\ProjectTaskFactory;
use App\Projects\Domain\Factory\ProjectTaskMerger;
use App\Shared\Domain\Bus\Event\DomainEvent;
use App\Shared\Domain\Event\ProjectParticipantWasAddedEvent;
use App\Shared\Domain\Event\TaskInformationWasChangedEvent;
use App\Shared\Domain\Event\TaskStatusWasChangedEvent;
use App\Shared\Domain\Event\TaskWasCreatedEvent;
use App\Shared\Domain\Exception\LogicException;
use App\Shared\Domain\Service\UuidGeneratorInterface;
use App\Shared\Domain\ValueObject\TaskId;
use App\Shared\Domain\ValueObject\UserId;

final class ProjectStateRecreator
{
    public function __construct(
        private readonly ProjectMerger $projectMerger,
        private readonly ProjectTaskFactory $projectTaskFactory,
        private readonly ProjectTaskMerger $projectTaskMerger,
        private readonly UuidGeneratorInterface $uuidGenerator,
    )
    {
    }

    public function fromEvent(Project $source, DomainEvent $event): Project
    {
        if ($event instanceof TaskWasCreatedEvent) {
            return $this->createTask($source, $event);
        }
        if ($event instanceof ProjectParticipantWasAddedEvent) {
            return $this->addParticipant($source, $event);
        }
        if ($event instanceof TaskInformationWasChangedEvent) {
            return $this->changeTaskDates($source, $event);
        }
        if ($event instanceof TaskStatusWasChangedEvent) {
            return $this->changeTaskStatus($source, $event);
        }

        throw new LogicException(sprintf('Invalid domain event "%s"', get_class($event)));
    }

    private function createTask(Project $source, TaskWasCreatedEvent $event): Project
    {
        $taskDto = new ProjectTaskDTO(
            $event->taskId,
            (int) $event->status,
            $event->ownerId,
            $event->startDate,
            $event->finishDate
        );

        $tasks = $source->getTasks()->add(
            $this->projectTaskFactory->create($this->uuidGenerator->generate(), $taskDto)
        );

        return $this->projectMerger->merge($source, new ProjectMergeDTO(
            tasks: $tasks->getInnerItems()
        ));
    }

    private function addParticipant(Project $source, ProjectParticipantWasAddedEvent $event): Project
    {
        $participants = $source->getParticipants()->add(new UserId($event->participantId));

        return $this->projectMerger->merge($source, new ProjectMergeDTO(
            participantIds: $participants->getInnerItems()
        ));
    }

    private function changeTaskDates(Project $source, TaskInformationWasChangedEvent $event): Project
    {
        $task = $source->getTasks()->get(new TaskId($event->taskId));
        // TODO add exception?
        if ($task === null) {
            return $source;
        }

        $tasks = $source->getTasks()->add(
            $this->projectTaskMerger->merge($task, new ProjectTaskMergeDTO(
                startDate: $event->startDate,
                finishDate: $event->finishDate,
            ))
        );

        return $this->projectMerger->merge($source, new ProjectMergeDTO(
            tasks: $tasks->getInnerItems()
        ));
    }

    private function changeTaskStatus(Project $source, TaskStatusWasChangedEvent $event): Project
    {
        $task = $source->getTasks()->get(new TaskId($event->taskId));
        // TODO add exception?
        if ($task === null) {
            return $source;
        }

        $tasks = $source->getTasks()->add(
            $this->projectTaskMerger->merge($task, new ProjectTaskMergeDTO(
                status: (int) $event->status,
            ))
        );

        return $this->projectMerger->merge($source, new ProjectMergeDTO(
            tasks: $tasks->getInnerItems()
        ));
    }
}
