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
use App\Shared\Domain\Factory\ProjectStatusFactory;
use App\Shared\Domain\Factory\TaskStatusFactory;
use App\Shared\Domain\ValueObject\TaskId;
use App\Shared\Domain\ValueObject\UserId;
use App\Tasks\Domain\DTO\TaskDTO;
use App\Tasks\Domain\DTO\TaskManagerDTO;
use App\Tasks\Domain\Entity\Task;
use App\Tasks\Domain\Entity\TaskManager;
use App\Tasks\Domain\Factory\TaskFactory;
use App\Tasks\Domain\Factory\TaskManagerFactory;

final class TaskManagerStateRecreator
{
    public function __construct(
        private readonly TaskManagerFactory $managerFactory,
        private readonly TaskFactory $taskFactory
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
        $dto = $this->createManagerDTO($source, [
            'status' => $event->status
        ]);
        return $this->managerFactory->create($dto);
    }

    private function changeOwner(TaskManager $source, ProjectOwnerWasChangedEvent $event): TaskManager
    {
        $dto = $this->createManagerDTO($source, [
            'ownerId' => $event->ownerId
        ]);
        return $this->managerFactory->create($dto);
    }

    private function removeParticipant(TaskManager $source, ProjectParticipantWasRemovedEvent $event): TaskManager
    {
        $participants = $source->getParticipants()->remove(new UserId($event->participantId));
        $dto = $this->createManagerDTO($source, [
            'participantIds' => $participants->getInnerItems()
        ]);

        return $this->managerFactory->create($dto);
    }

    private function addParticipant(TaskManager $source, ProjectParticipantWasAddedEvent $event): TaskManager
    {
        $participants = $source->getParticipants()->add(new UserId($event->participantId));
        $dto = $this->createManagerDTO($source, [
            'participantIds' => $participants->getInnerItems()
        ]);

        return $this->managerFactory->create($dto);
    }

    private function changeFinishDate(TaskManager $source, ProjectInformationWasChangedEvent $event): TaskManager
    {
        $dto = $this->createManagerDTO($source, [
            'finishDate' => $event->finishDate
        ]);
        return $this->managerFactory->create($dto);
    }

    private function changeTaskStartDate(TaskManager $source, ProjectTaskStartDateWasChangedEvent $event): TaskManager
    {
        /** @var Task $task */
        $task = $source->getTasks()->get(new TaskId($event->taskId));
        //TODO throw exception?
        if ($task === null) {
            return $source;
        }

        $taskDto = $this->createTaskDTO($task, [
            'startDate' => $event->startDate
        ]);
        $tasks = $source->getTasks()->add(
            $this->taskFactory->create($taskDto)
        );

        $dto = $this->createManagerDTO($source, [
            'tasks' => $tasks->getInnerItems()
        ]);
        return $this->managerFactory->create($dto);
    }

    private function changeTaskFinishDate(TaskManager $source, ProjectTaskFinishDateWasChangedEvent $event): TaskManager
    {
        /** @var Task $task */
        $task = $source->getTasks()->get(new TaskId($event->taskId));
        //TODO throw exception?
        if ($task === null) {
            return $source;
        }

        $taskDto = $this->createTaskDTO($task, [
            'finishDate' => $event->finishDate
        ]);
        $tasks = $source->getTasks()->add(
            $this->taskFactory->create($taskDto)
        );

        $dto = $this->createManagerDTO($source, [
            'tasks' => $tasks->getInnerItems()
        ]);
        return $this->managerFactory->create($dto);
    }

    private function changeTaskStatus(TaskManager $source, ProjectTaskStatusWasChangedEvent $event): TaskManager
    {
        /** @var Task $task */
        $task = $source->getTasks()->get(new TaskId($event->taskId));
        //TODO throw exception?
        if ($task === null) {
            return $source;
        }

        $taskDto = $this->createTaskDTO($task, [
            'status' => $event->status
        ]);
        $tasks = $source->getTasks()->add(
            $this->taskFactory->create($taskDto)
        );

        $dto = $this->createManagerDTO($source, [
            'tasks' => $tasks->getInnerItems()
        ]);
        return $this->managerFactory->create($dto);
    }

    private function createManagerDTO(TaskManager $source, array $attributes): TaskManagerDTO
    {
        return new TaskManagerDTO(
            $attributes['id'] ?? $source->getId()->value,
            $attributes['projectId'] ?? $source->getProjectId()->value,
            (int) $attributes['status'] ?? ProjectStatusFactory::scalarFromObject($source->getStatus()),
            $attributes['ownerId'] ?? $source->getOwner()->userId->value,
            $attributes['finishDate'] ?? $source->getFinishDate()->getValue(),
            $attributes['participantIds'] ?? $source->getParticipants()->getInnerItems(),
            $attributes['tasks'] ?? $source->getTasks()->getInnerItems()
        );
    }

    private function createTaskDTO(Task $source, array $attributes): TaskDTO
    {
        return new TaskDTO(
            $attributes['id'] ?? $source->getId()->value,
            $attributes['name'] ?? $source->getInformation()->name->value,
            $attributes['brief'] ?? $source->getInformation()->brief->value,
            $attributes['description'] ?? $source->getInformation()->description->value,
            $attributes['startDate'] ?? $source->getInformation()->startDate->getValue(),
            $attributes['finishDate'] ?? $source->getInformation()->finishDate->getValue(),
            $attributes['ownerId'] ?? $source->getOwnerId()->value,
            (int) $attributes['status'] ?? TaskStatusFactory::scalarFromObject($source->getStatus()),
            $attributes['links'] ?? $source->getLinks()
        );
    }
}
