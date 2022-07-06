<?php
declare(strict_types=1);

namespace App\Shared\Application\Subscriber;

use App\Shared\Domain\Bus\Event\EventBusInterface;
use App\Shared\Domain\Bus\Event\EventSubscriberInterface;
use App\Shared\Domain\Entity\SharedUser;
use App\Shared\Domain\Repository\SharedUserRepositoryInterface;
use App\Shared\Domain\ValueObject\UserEmail;
use App\Shared\Domain\ValueObject\UserFirstname;
use App\Shared\Domain\ValueObject\UserId;
use App\Shared\Domain\ValueObject\UserLastname;
use App\Users\Domain\Event\UserWasCreatedEvent;

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
        $user = SharedUser::create(
            new UserId($event->aggregateId),
            new UserEmail($event->email),
            new UserFirstname($event->firstname),
            new UserLastname($event->lastname)
        );

        $this->userRepository->save($user);
        $this->eventBus->dispatch(...$user->releaseEvents());
    }
}
