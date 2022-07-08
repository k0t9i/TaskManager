<?php
declare(strict_types=1);

namespace App\Projects\Application\Service;

use App\Projects\Domain\DTO\ProjectDTO;
use App\Projects\Domain\DTO\ProjectTaskDTO;
use App\Projects\Domain\Entity\Project;
use App\Projects\Domain\Entity\ProjectTask;
use App\Projects\Domain\Factory\ProjectFactory;
use App\Projects\Domain\Factory\ProjectTaskFactory;
use App\Shared\Domain\Bus\Event\DomainEvent;
use App\Shared\Domain\Event\ProjectParticipantWasAddedEvent;
use App\Shared\Domain\Event\TaskInformationWasChangedEvent;
use App\Shared\Domain\Event\TaskStatusWasChangedEvent;
use App\Shared\Domain\Event\TaskWasCreatedEvent;
use App\Shared\Domain\Exception\LogicException;
use App\Shared\Domain\Factory\ProjectStatusFactory;
use App\Shared\Domain\Factory\TaskStatusFactory;
use App\Shared\Domain\Service\UuidGeneratorInterface;
use App\Shared\Domain\ValueObject\TaskId;
use App\Shared\Domain\ValueObject\UserId;

final class ProjectStateRecreator
{
    public function __construct(
        private readonly ProjectFactory $projectFactory,
        private readonly ProjectTaskFactory $projectTaskFactory,
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

        $dto = $this->createProjectDTO($source, [
            'tasks' => $tasks->getInnerItems()
        ]);
        return $this->projectFactory->create($dto);
    }

    private function addParticipant(Project $source, ProjectParticipantWasAddedEvent $event): Project
    {
        $participants = $source->getParticipants()->add(new UserId($event->participantId));

        $dto = $this->createProjectDTO($source, [
            'participantIds' => $participants->getInnerItems()
        ]);
        return $this->projectFactory->create($dto);
    }

    private function changeTaskDates(Project $source, TaskInformationWasChangedEvent $event): Project
    {
        $task = $source->getTasks()->get(new TaskId($event->taskId));
        // TODO add exception?
        if ($task === null) {
            return $source;
        }

        $taskDto = $this->createProjectTaskDTO($task, [
            'startDate' => $event->startDate,
            'finishDate' => $event->finishDate,
        ]);
        $tasks = $source->getTasks()->add(
            $this->projectTaskFactory->create($task->getId()->value, $taskDto)
        );

        $dto = $this->createProjectDTO($source, [
            'tasks' => $tasks->getInnerItems()
        ]);
        return $this->projectFactory->create($dto);
    }

    private function changeTaskStatus(Project $source, TaskStatusWasChangedEvent $event): Project
    {
        $task = $source->getTasks()->get(new TaskId($event->taskId));
        // TODO add exception?
        if ($task === null) {
            return $source;
        }

        $taskDto = $this->createProjectTaskDTO($task, [
            'status' => $event->status
        ]);
        $tasks = $source->getTasks()->add(
            $this->projectTaskFactory->create($task->getId()->value, $taskDto)
        );

        $dto = $this->createProjectDTO($source, [
            'tasks' => $tasks->getInnerItems()
        ]);
        return $this->projectFactory->create($dto);
    }

    private function createProjectDTO(Project $source, array $attributes): ProjectDTO
    {
        return new ProjectDTO(
            $attributes['id'] ?? $source->getId()->value,
            $attributes['name'] ?? $source->getInformation()->name->value,
            $attributes['description'] ?? $source->getInformation()->description->value,
            $attributes['finishDate'] ?? $source->getInformation()->finishDate->getValue(),
            $attributes['status'] ?? ProjectStatusFactory::scalarFromObject($source->getStatus()),
            $attributes['ownerId'] ?? $source->getOwner()->userId->value,
            $attributes['participantIds'] ?? $source->getParticipants()->getInnerItems(),
            $attributes['tasks'] ?? $source->getTasks()->getInnerItems()
        );
    }

    private function createProjectTaskDTO(ProjectTask $source, array $attributes): ProjectTaskDTO
    {
        return new ProjectTaskDTO(
            $attributes['id'] ?? $source->getTaskId()->value,
            (int) $attributes['status'] ?? TaskStatusFactory::scalarFromObject($source->getStatus()),
            $attributes['ownerId'] ?? $source->getOwnerId()->value,
            $attributes['startDate'] ?? $source->getStartDate()->getValue(),
            $attributes['finishDate'] ?? $source->getFinishDate()->getValue()
        );
    }
}
