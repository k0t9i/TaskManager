<?php
declare(strict_types=1);

namespace App\Projections\Application\Subscriber;

use App\Projections\Domain\Repository\ProjectProjectionRepositoryInterface;
use App\Shared\Domain\Bus\Event\DomainEvent;
use App\Shared\Domain\Bus\Event\EventSubscriberInterface;
use App\Shared\Domain\Event\Requests\RequestWasCreatedEvent;

final class ChangeProjectProjectionOnRequestCreatedSubscriber implements EventSubscriberInterface
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
        return [RequestWasCreatedEvent::class];
    }

    public function __invoke(RequestWasCreatedEvent $event): void
    {
        $projections = $this->projectionRepository->findAllById($event->projectId);

        foreach ($projections as $projection) {
            $projection->incrementPendingRequestsCount();
            $this->projectionRepository->save($projection);
        }
    }
}
