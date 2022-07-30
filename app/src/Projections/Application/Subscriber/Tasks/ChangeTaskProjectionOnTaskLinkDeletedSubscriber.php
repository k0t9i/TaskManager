<?php

declare(strict_types=1);

namespace App\Projections\Application\Subscriber\Tasks;

use App\Projections\Domain\Repository\TaskProjectionRepositoryInterface;
use App\Shared\Application\Bus\Event\EventSubscriberInterface;
use App\Shared\Domain\Event\DomainEvent;
use App\Shared\Domain\Event\Tasks\TaskLinkWasDeletedEvent;
use App\Shared\Domain\Exception\TaskNotExistException;

final class ChangeTaskProjectionOnTaskLinkDeletedSubscriber implements EventSubscriberInterface
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
        return [TaskLinkWasDeletedEvent::class];
    }

    public function __invoke(TaskLinkWasDeletedEvent $event): void
    {
        $projection = $this->projectionRepository->findById($event->fromTaskId);
        if (null === $projection) {
            throw new TaskNotExistException($event->fromTaskId);
        }

        $projection->decrementLinksCount();
        $this->projectionRepository->save($projection);
    }
}
