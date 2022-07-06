<?php
declare(strict_types=1);

namespace App\Tasks\Application\Subscriber;

use App\Shared\Domain\Bus\Event\DomainEvent;
use App\Shared\Domain\Bus\Event\EventBusInterface;
use App\Shared\Domain\Bus\Event\EventSubscriberInterface;
use App\Shared\Domain\Event\ProjectTaskStatusWasChangedEvent;
use App\Shared\Domain\ValueObject\ProjectId;
use App\Tasks\Application\Service\TaskManagerTaskStatusChanger;
use App\Tasks\Domain\Exception\TaskManagerNotExistException;
use App\Tasks\Domain\Repository\TaskManagerRepositoryInterface;

final class ChangeTaskStatusOnProjectTaskStatusChangedSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly TaskManagerRepositoryInterface $managerRepository,
        private readonly TaskManagerTaskStatusChanger $taskStatusChanger,
        private readonly EventBusInterface $eventBus
    ) {
    }

    /**
     * @return DomainEvent[]
     */
    public function subscribeTo(): array
    {
        return [ProjectTaskStatusWasChangedEvent::class];
    }

    public function __invoke(ProjectTaskStatusWasChangedEvent $event): void
    {
        $manager = $this->managerRepository->findByProjectId(new ProjectId($event->aggregateId));
        if ($manager === null) {
            throw new TaskManagerNotExistException();
        }

        $newManager = $this->taskStatusChanger->changeStatus($manager, $event->taskId, (int) $event->status);

        $this->managerRepository->save($newManager);
        $this->eventBus->dispatch(...$newManager->releaseEvents());
    }
}
