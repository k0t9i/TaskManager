<?php

declare(strict_types=1);

namespace App\Projections\Application\Subscriber\Users;

use App\Projections\Domain\Entity\UserProjection;
use App\Projections\Domain\Repository\UserProjectionRepositoryInterface;
use App\Shared\Application\Bus\Event\EventSubscriberInterface;
use App\Shared\Application\Service\UuidGeneratorInterface;
use App\Shared\Domain\Event\Users\UserWasCreatedEvent;
use Exception;

final class CreateUserProjectionOnUserCreated implements EventSubscriberInterface
{
    public function __construct(
        private readonly UserProjectionRepositoryInterface $userRepository,
        private readonly UuidGeneratorInterface $uuidGenerator
    ) {
    }

    public function subscribeTo(): array
    {
        return [UserWasCreatedEvent::class];
    }

    /**
     * @throws Exception
     */
    public function __invoke(UserWasCreatedEvent $event): void
    {
        $projection = new UserProjection(
            $this->uuidGenerator->generate(),
            $event->firstname,
            $event->lastname,
            $event->email,
            $event->aggregateId
        );

        $this->userRepository->save($projection);
    }
}
