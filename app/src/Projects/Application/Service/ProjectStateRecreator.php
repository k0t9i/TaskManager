<?php
declare(strict_types=1);

namespace App\Projects\Application\Service;

use App\Projects\Domain\DTO\ProjectMergeDTO;
use App\Projects\Domain\DTO\ProjectTaskDTO;
use App\Projects\Domain\Entity\Project;
use App\Projects\Domain\Factory\ProjectMerger;
use App\Projects\Domain\Factory\ProjectTaskFactory;
use App\Shared\Application\Bus\Event\DomainEvent;
use App\Shared\Domain\Event\Projects\ProjectParticipantWasAddedEvent;
use App\Shared\Domain\Event\Requests\RequestStatusWasChangedEvent;
use App\Shared\Domain\Event\Tasks\TaskWasCreatedEvent;
use App\Shared\Domain\Exception\LogicException;
use App\Shared\Domain\Service\UuidGeneratorInterface;
use App\Shared\Domain\ValueObject\Requests\RequestStatus;
use App\Shared\Domain\ValueObject\Users\UserId;

final class ProjectStateRecreator
{
    public function __construct(
        private readonly ProjectMerger $projectMerger,
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
        if ($event instanceof RequestStatusWasChangedEvent) {
            return $this->tryToAddParticipant($source, $event);
        }

        throw new LogicException(sprintf('Invalid domain event "%s"', get_class($event)));
    }

    private function createTask(Project $source, TaskWasCreatedEvent $event): Project
    {
        $taskDto = new ProjectTaskDTO(
            $event->taskId,
            $event->ownerId
        );

        $tasks = $source->getTasks()->add(
            $this->projectTaskFactory->create($this->uuidGenerator->generate(), $taskDto)
        );

        return $this->projectMerger->merge($source, new ProjectMergeDTO(
            tasks: $tasks->getInnerItems()
        ));
    }

    private function tryToAddParticipant(Project $source, RequestStatusWasChangedEvent $event): Project
    {
        $status = RequestStatus::createFromScalar((int)$event->status);
        if (!$status->isConfirmed()) {
            return $source;
        }

        $participants = $source->getParticipants()->add(new UserId($event->userId));

        $project = $this->projectMerger->merge($source, new ProjectMergeDTO(
            participantIds: $participants->getInnerItems()
        ));
        $project->registerEvent(new ProjectParticipantWasAddedEvent(
            $project->getId()->value,
            $event->userId
        ));
        return $project;
    }
}
