<?php
declare(strict_types=1);

namespace App\Projections\Application\Subscriber\Users;

use App\Projections\Domain\Repository\UserProjectionRepositoryInterface;
use App\Shared\Application\Bus\Event\EventSubscriberInterface;
use App\Shared\Domain\Event\DomainEvent;
use App\Shared\Domain\Event\Projects\ProjectParticipantWasRemovedEvent;
use App\Shared\Domain\Exception\UserNotExistException;

final class ChangeUserProjectionOnParticipantRemovedSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly UserProjectionRepositoryInterface $userRepository
    ) {
    }

    /**
     * @return DomainEvent[]
     */
    public function subscribeTo(): array
    {
        return [ProjectParticipantWasRemovedEvent::class];
    }

    public function __invoke(ProjectParticipantWasRemovedEvent $event): void
    {
        $projection = $this->userRepository->findByUserIdAndProjectId($event->participantId, $event->aggregateId);
        if ($projection === null) {
            throw new UserNotExistException($event->participantId);
        }

        $this->userRepository->delete($projection);
    }
}
