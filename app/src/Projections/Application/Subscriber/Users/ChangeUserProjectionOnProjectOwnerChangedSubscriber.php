<?php

declare(strict_types=1);

namespace App\Projections\Application\Subscriber\Users;

use App\Projections\Domain\Repository\UserProjectionRepositoryInterface;
use App\Shared\Application\Bus\Event\EventSubscriberInterface;
use App\Shared\Domain\Event\Projects\ProjectOwnerWasChangedEvent;
use App\Shared\Domain\Exception\UserNotExistException;

final class ChangeUserProjectionOnProjectOwnerChangedSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly UserProjectionRepositoryInterface $userRepository
    ) {
    }

    public function subscribeTo(): array
    {
        return [ProjectOwnerWasChangedEvent::class];
    }

    public function __invoke(ProjectOwnerWasChangedEvent $event): void
    {
        $oldProjection = $this->userRepository->findByUserId($event->ownerId);
        if (null === $oldProjection) {
            throw new UserNotExistException($event->ownerId);
        }

        $projections = $this->userRepository->findAllByProjectId($event->aggregateId);
        foreach ($projections as $projection) {
            $projection->updateOwner($event->ownerId, $oldProjection);
            $this->userRepository->save($projection);
        }
    }
}
