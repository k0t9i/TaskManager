<?php

declare(strict_types=1);

namespace App\Projections\Application\Subscriber\Tasks;

use App\Projections\Domain\Repository\TaskProjectionRepositoryInterface;
use App\Shared\Application\Bus\Event\EventSubscriberInterface;
use App\Shared\Domain\Event\DomainEvent;
use App\Shared\Domain\Event\Users\UserProfileWasChangedEvent;

final class ChangeTaskProjectionOnUserProfileChangedSubscriber implements EventSubscriberInterface
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
        return [UserProfileWasChangedEvent::class];
    }

    public function __invoke(UserProfileWasChangedEvent $event): void
    {
        $projections = $this->projectionRepository->findAllByOwnerId($event->aggregateId);

        foreach ($projections as $projection) {
            $projection->changeOwnerProfile($event->firstname, $event->lastname);
            $this->projectionRepository->save($projection);
        }
    }
}
