<?php

declare(strict_types=1);

namespace App\Projections\Application\Subscriber\Projects;

use App\Projections\Domain\Repository\ProjectProjectionRepositoryInterface;
use App\Shared\Application\Bus\Event\EventSubscriberInterface;
use App\Shared\Domain\Event\Requests\RequestWasCreatedEvent;

final class ChangeProjectProjectionOnRequestCreatedSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly ProjectProjectionRepositoryInterface $projectionRepository,
    ) {
    }

    public function subscribeTo(): array
    {
        return [RequestWasCreatedEvent::class];
    }

    public function __invoke(RequestWasCreatedEvent $event): void
    {
        $projections = $this->projectionRepository->findAllById($event->aggregateId);

        foreach ($projections as $projection) {
            $projection->incrementPendingRequestsCount();
            $this->projectionRepository->save($projection);
        }
    }
}
