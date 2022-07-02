<?php
declare(strict_types=1);

namespace App\Projects\Application\Subscriber;

use App\Projects\Application\Factory\ProjectTaskStatusChanger;
use App\Projects\Domain\Repository\ProjectRepositoryInterface;
use App\Shared\Domain\Bus\Event\EventBusInterface;
use App\Shared\Domain\Bus\Event\EventSubscriberInterface;
use App\Shared\Domain\Exception\ProjectNotExistException;
use App\Shared\Domain\ValueObject\ProjectId;
use App\Tasks\Domain\Event\TaskStatusWasChangedEvent;
use App\Tasks\Domain\Event\TaskWasCreatedEvent;

final class ChangeTaskStatusOnTaskStatusChangedSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly ProjectRepositoryInterface $projectRepository,
        private readonly ProjectTaskStatusChanger $taskStatusChanger,
        private readonly EventBusInterface $eventBus
    ) {
    }

    public function subscribeTo(): array
    {
        return [TaskWasCreatedEvent::class];
    }

    public function __invoke(TaskStatusWasChangedEvent $event): void
    {
        $project = $this->projectRepository->findById(new ProjectId($event->projectId));
        if ($project === null) {
            throw new ProjectNotExistException();
        }

        $project = $this->taskStatusChanger->changeStatus($project, $event->taskId, (int) $event->status);

        $this->projectRepository->save($project);
        $this->eventBus->dispatch(...$project->releaseEvents());
    }
}
