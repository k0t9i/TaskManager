<?php
declare(strict_types=1);

namespace App\Projects\Application\Subscriber;

use App\Projects\Application\Factory\ProjectTaskDTO;
use App\Projects\Application\Service\ProjectTaskCreator;
use App\Projects\Domain\Repository\ProjectRepositoryInterface;
use App\Shared\Domain\Bus\Event\EventBusInterface;
use App\Shared\Domain\Bus\Event\EventSubscriberInterface;
use App\Shared\Domain\Exception\ProjectNotExistException;
use App\Shared\Domain\ValueObject\ProjectId;
use App\Tasks\Domain\Event\TaskWasCreatedEvent;

final class CreateTaskOnTaskCreatedSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly ProjectRepositoryInterface $projectRepository,
        private readonly ProjectTaskCreator $projectTaskCreator,
        private readonly EventBusInterface $eventBus
    ) {
    }

    public function subscribeTo(): array
    {
        return [TaskWasCreatedEvent::class];
    }

    public function __invoke(TaskWasCreatedEvent $event): void
    {
        $project = $this->projectRepository->findById(new ProjectId($event->projectId));
        if ($project === null) {
            throw new ProjectNotExistException();
        }

        $taskDto = new ProjectTaskDTO(
            $event->taskId,
            (int) $event->status,
            $event->ownerId,
            $event->startDate,
            $event->finishDate
        );
        $project = $this->projectTaskCreator->createTask($project, $taskDto);

        $this->projectRepository->save($project);
        $this->eventBus->dispatch(...$project->releaseEvents());
    }
}
