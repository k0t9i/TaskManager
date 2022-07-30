<?php

declare(strict_types=1);

namespace App\Projections\Application\Subscriber\Projects;

use App\Projections\Domain\Repository\ProjectProjectionRepositoryInterface;
use App\Shared\Application\Bus\Event\EventSubscriberInterface;
use App\Shared\Domain\Event\DomainEvent;
use App\Shared\Domain\Event\Projects\ProjectStatusWasChangedEvent;

final class ChangeProjectProjectionOnProjectStatusChangedSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly ProjectProjectionRepositoryInterface $projectionRepository,
    ) {
    }

    /**
     * @return DomainEvent[]
     */
    public function subscribeTo(): array
    {
        return [ProjectStatusWasChangedEvent::class];
    }

    public function __invoke(ProjectStatusWasChangedEvent $event): void
    {
        $projections = $this->projectionRepository->findAllById($event->aggregateId);

        foreach ($projections as $projection) {
            $projection->changeStatus((int) $event->status);
            $this->projectionRepository->save($projection);
        }
    }
}
