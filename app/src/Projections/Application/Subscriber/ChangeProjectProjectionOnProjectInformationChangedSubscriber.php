<?php
declare(strict_types=1);

namespace App\Projections\Application\Subscriber;

use App\Projections\Domain\Repository\ProjectProjectionRepositoryInterface;
use App\Shared\Domain\Bus\Event\DomainEvent;
use App\Shared\Domain\Bus\Event\EventSubscriberInterface;
use App\Shared\Domain\Event\Projects\ProjectInformationWasChangedEvent;

final class ChangeProjectProjectionOnProjectInformationChangedSubscriber implements EventSubscriberInterface
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
        return [ProjectInformationWasChangedEvent::class];
    }

    public function __invoke(ProjectInformationWasChangedEvent $event): void
    {
        $projections = $this->projectionRepository->findAllById($event->aggregateId);

        foreach ($projections as $projection) {
            $projection->updateInformation($event->name, $event->finishDate);
            $this->projectionRepository->save($projection);
        }
    }
}
