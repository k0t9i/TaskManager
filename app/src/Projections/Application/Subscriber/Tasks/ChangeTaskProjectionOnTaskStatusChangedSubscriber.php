<?php
declare(strict_types=1);

namespace App\Projections\Application\Subscriber\Tasks;

use App\Projections\Domain\Repository\TaskProjectionRepositoryInterface;
use App\Shared\Application\Bus\Event\EventSubscriberInterface;
use App\Shared\Domain\Event\DomainEvent;
use App\Shared\Domain\Event\Tasks\TaskStatusWasChangedEvent;
use App\Shared\Domain\Exception\TaskNotExistException;

final class ChangeTaskProjectionOnTaskStatusChangedSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly TaskProjectionRepositoryInterface $projectionRepository,
    ) {
    }

    /**
     * @return DomainEvent[]
     */
    public function subscribeTo(): array
    {
        return [TaskStatusWasChangedEvent::class];
    }

    public function __invoke(TaskStatusWasChangedEvent $event): void
    {
        $projection = $this->projectionRepository->findById($event->taskId);
        if ($projection === null) {
            throw new TaskNotExistException($event->taskId);
        }

        if ($projection) {
            $projection->changeStatus((int) $event->status);
            $this->projectionRepository->save($projection);
        }
    }
}
