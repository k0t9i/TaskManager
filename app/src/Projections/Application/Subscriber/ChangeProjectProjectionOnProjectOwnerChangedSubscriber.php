<?php
declare(strict_types=1);

namespace App\Projections\Application\Subscriber;

use App\Projections\Domain\Repository\ProjectProjectionRepositoryInterface;
use App\Shared\Domain\Bus\Event\DomainEvent;
use App\Shared\Domain\Bus\Event\EventSubscriberInterface;
use App\Shared\Domain\Event\Projects\ProjectOwnerWasChangedEvent;
use App\Shared\Domain\Repository\SharedUserRepositoryInterface;
use App\Shared\Domain\ValueObject\Users\UserId;

final class ChangeProjectProjectionOnProjectOwnerChangedSubscriber implements EventSubscriberInterface
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
        return [ProjectOwnerWasChangedEvent::class];
    }

    public function __invoke(ProjectOwnerWasChangedEvent $event): void
    {
        $user = $this->userRepository->findById(new UserId($event->ownerId));
        $projections = $this->projectionRepository->findAllById($event->aggregateId);

        foreach ($projections as $projection) {
            $projection->changeOwner(
                $event->ownerId,
                $user ? $user->getFirstname()->value : '',
                $user ? $user->getLastname()->value : '',
                $user ? $user->getEmail()->value : ''
            );
            $this->projectionRepository->save($projection);
        }
    }
}
