<?php

declare(strict_types=1);

namespace App\Projections\Application\Subscriber\Requests;

use App\Projections\Domain\Repository\RequestProjectionRepositoryInterface;
use App\Projections\Domain\Repository\UserProjectionRepositoryInterface;
use App\Shared\Application\Bus\Event\EventSubscriberInterface;
use App\Shared\Domain\Event\DomainEvent;
use App\Shared\Domain\Event\Users\UserProfileWasChangedEvent;
use App\Shared\Domain\Exception\UserNotExistException;
use Exception;

final class ChangeOnUserProfileChanged implements EventSubscriberInterface
{
    public function __construct(
        private readonly RequestProjectionRepositoryInterface $requestRepository,
        private readonly UserProjectionRepositoryInterface $userRepository
    ) {
    }

    /**
     * @return DomainEvent[]
     */
    public function subscribeTo(): array
    {
        return [UserProfileWasChangedEvent::class];
    }

    /**
     * @throws Exception
     */
    public function __invoke(UserProfileWasChangedEvent $event): void
    {
        $user = $this->userRepository->findByUserId($event->aggregateId);
        if (null === $user) {
            throw new UserNotExistException($event->aggregateId);
        }

        $projections = $this->requestRepository->findByUserId($event->aggregateId);
        foreach ($projections as $projection) {
            $projection->changeUserProfile($event->firstname, $event->lastname);
            $this->requestRepository->save($projection);
        }
    }
}
