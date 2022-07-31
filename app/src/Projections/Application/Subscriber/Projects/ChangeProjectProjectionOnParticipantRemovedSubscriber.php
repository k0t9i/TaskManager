<?php

declare(strict_types=1);

namespace App\Projections\Application\Subscriber\Projects;

use App\Projections\Domain\Repository\ProjectProjectionRepositoryInterface;
use App\Shared\Application\Bus\Event\EventSubscriberInterface;
use App\Shared\Domain\Event\Projects\ProjectParticipantWasRemovedEvent;

final class ChangeProjectProjectionOnParticipantRemovedSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly ProjectProjectionRepositoryInterface $projectionRepository,
    ) {
    }

    public function subscribeTo(): array
    {
        return [ProjectParticipantWasRemovedEvent::class];
    }

    public function __invoke(ProjectParticipantWasRemovedEvent $event): void
    {
        $projections = $this->projectionRepository->findAllById($event->aggregateId);

        foreach ($projections as $projection) {
            if ($projection->getUserId() === $event->participantId) {
                $this->projectionRepository->delete($projection);
            } else {
                $projection->decrementParticipantsCount();
                $this->projectionRepository->save($projection);
            }
        }
    }
}
