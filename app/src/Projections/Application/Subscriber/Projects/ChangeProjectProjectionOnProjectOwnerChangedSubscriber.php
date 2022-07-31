<?php

declare(strict_types=1);

namespace App\Projections\Application\Subscriber\Projects;

use App\Projections\Domain\Repository\ProjectProjectionRepositoryInterface;
use App\Projections\Domain\Repository\UserProjectionRepositoryInterface;
use App\Shared\Application\Bus\Event\EventSubscriberInterface;
use App\Shared\Domain\Event\Projects\ProjectOwnerWasChangedEvent;
use App\Shared\Domain\Exception\UserNotExistException;

final class ChangeProjectProjectionOnProjectOwnerChangedSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly ProjectProjectionRepositoryInterface $projectionRepository,
        private readonly UserProjectionRepositoryInterface $userRepository
    ) {
    }

    public function subscribeTo(): array
    {
        return [ProjectOwnerWasChangedEvent::class];
    }

    public function __invoke(ProjectOwnerWasChangedEvent $event): void
    {
        $user = $this->userRepository->findByUserId($event->ownerId);
        if (null === $user) {
            throw new UserNotExistException($event->ownerId);
        }
        $projections = $this->projectionRepository->findAllById($event->aggregateId);

        foreach ($projections as $projection) {
            $projection->changeOwner(
                $event->ownerId,
                $user->getFirstname(),
                $user->getLastname(),
                $user->getEmail()
            );
            $this->projectionRepository->save($projection);
        }
    }
}
