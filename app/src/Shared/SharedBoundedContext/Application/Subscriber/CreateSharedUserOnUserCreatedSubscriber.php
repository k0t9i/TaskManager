<?php
declare(strict_types=1);

namespace App\Shared\SharedBoundedContext\Application\Subscriber;

use App\Shared\Application\Bus\Event\EventBusInterface;
use App\Shared\Application\Bus\Event\EventSubscriberInterface;
use App\Shared\Domain\Event\Users\UserWasCreatedEvent;
use App\Shared\SharedBoundedContext\Domain\Entity\SharedUser;
use App\Shared\SharedBoundedContext\Domain\Repository\SharedUserRepositoryInterface;

final class CreateSharedUserOnUserCreatedSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly SharedUserRepositoryInterface $userRepository,
        private readonly EventBusInterface $eventBus,
    ) {
    }

    public function subscribeTo(): array
    {
        return [UserWasCreatedEvent::class];
    }

    public function __invoke(UserWasCreatedEvent $event): void
    {
        $user = new SharedUser($event->aggregateId);

        $this->userRepository->save($user);
        $this->eventBus->dispatch(...$user->releaseEvents());
    }
}
