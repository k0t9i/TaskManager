<?php

declare(strict_types=1);

namespace App\Projections\Application\Subscriber\Users;

use App\Projections\Domain\Repository\UserProjectionRepositoryInterface;
use App\Shared\Application\Bus\Event\EventSubscriberInterface;
use App\Shared\Application\Service\UuidGeneratorInterface;
use App\Shared\Domain\Event\DomainEvent;
use App\Shared\Domain\Event\Projects\ProjectWasCreatedEvent;
use App\Shared\Domain\Exception\UserNotExistException;

final class ChangeUserProjectionOnProjectCreatedSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly UserProjectionRepositoryInterface $userRepository,
        private readonly UuidGeneratorInterface $uuidGenerator
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
        $oldProjection = $this->userRepository->findByUserId($event->ownerId);
        if (null === $oldProjection) {
            throw new UserNotExistException($event->ownerId);
        }
        $projection = $oldProjection->createCopyForProject(
            $this->uuidGenerator->generate(),
            $event->aggregateId
        );
        $projection->updateOwner($event->ownerId, $oldProjection);
        $this->userRepository->save($projection);
    }
}
