<?php
declare(strict_types=1);

namespace App\Projections\Application\Subscriber;

use App\Projections\Domain\Entity\ProjectProjection;
use App\Projections\Domain\Repository\ProjectProjectionRepositoryInterface;
use App\Shared\Domain\Bus\Event\DomainEvent;
use App\Shared\Domain\Bus\Event\EventSubscriberInterface;
use App\Shared\Domain\Event\Projects\ProjectWasCreatedEvent;
use App\Shared\Domain\Repository\SharedUserRepositoryInterface;
use App\Shared\Domain\ValueObject\Users\UserId;

final class CreateProjectProjectionOnProjectCreated implements EventSubscriberInterface
{
    public function __construct(
        private readonly ProjectProjectionRepositoryInterface $projectionRepository,
        private readonly SharedUserRepositoryInterface $userRepository
    ) {
    }

    /**
     * @return DomainEvent[]
     */
    public function subscribeTo(): array
    {
        return [ProjectWasCreatedEvent::class];
    }

    public function __invoke(ProjectWasCreatedEvent $event): void
    {
        $user = $this->userRepository->findById(new UserId($event->ownerId));

        $projection = new ProjectProjection(
            $event->aggregateId,
            $event->ownerId,
            $event->name,
            $event->finishDate,
            (int) $event->status,
            $event->ownerId,
            $user ? $user->getFirstname()->value : '',
            $user ? $user->getLastname()->value : '',
            $user ? $user->getEmail()->value : ''
        );

        $this->projectionRepository->save($projection);
    }
}
