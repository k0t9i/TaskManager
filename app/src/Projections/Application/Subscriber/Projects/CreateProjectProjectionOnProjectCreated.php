<?php

declare(strict_types=1);

namespace App\Projections\Application\Subscriber\Projects;

use App\Projections\Domain\Entity\ProjectProjection;
use App\Projections\Domain\Repository\ProjectProjectionRepositoryInterface;
use App\Projections\Domain\Repository\UserProjectionRepositoryInterface;
use App\Shared\Application\Bus\Event\EventSubscriberInterface;
use App\Shared\Domain\Event\Projects\ProjectWasCreatedEvent;
use App\Shared\Domain\Exception\UserNotExistException;
use DateTime;
use Exception;

final class CreateProjectProjectionOnProjectCreated implements EventSubscriberInterface
{
    public function __construct(
        private readonly ProjectProjectionRepositoryInterface $projectionRepository,
        private readonly UserProjectionRepositoryInterface $userRepository
    ) {
    }

    public function subscribeTo(): array
    {
        return [ProjectWasCreatedEvent::class];
    }

    /**
     * @throws Exception
     */
    public function __invoke(ProjectWasCreatedEvent $event): void
    {
        $user = $this->userRepository->findByUserId($event->ownerId);
        if (null === $user) {
            throw new UserNotExistException($event->ownerId);
        }

        $projection = new ProjectProjection(
            $event->aggregateId,
            $event->ownerId,
            $event->name,
            $event->description,
            new DateTime($event->finishDate),
            (int) $event->status,
            $event->ownerId,
            $user->getFirstname(),
            $user->getLastname(),
            $user->getEmail()
        );

        $this->projectionRepository->save($projection);
    }
}
