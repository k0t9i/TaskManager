<?php
declare(strict_types=1);

namespace App\Projections\Application\Subscriber\Projects;

use App\Projections\Domain\Repository\ProjectProjectionRepositoryInterface;
use App\Shared\Application\Bus\Event\DomainEvent;
use App\Shared\Application\Bus\Event\EventSubscriberInterface;
use App\Shared\Domain\Event\Projects\ProjectParticipantWasAddedEvent;

final class ChangeProjectProjectionOnParticipantAddedSubscriber implements EventSubscriberInterface
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
        return [ProjectParticipantWasAddedEvent::class];
    }

    public function __invoke(ProjectParticipantWasAddedEvent $event): void
    {
        $projections = $this->projectionRepository->findAllById($event->aggregateId);

        $first = true;
        foreach ($projections as $projection) {
            $projection->incrementParticipantsCount();
            $this->projectionRepository->save($projection);
            if ($first) {
                $this->projectionRepository->save($projection->createCopyForUser($event->participantId));
                $first = false;
            }
        }
    }
}
