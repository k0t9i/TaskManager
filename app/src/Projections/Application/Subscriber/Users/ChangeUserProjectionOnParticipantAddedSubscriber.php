<?php

declare(strict_types=1);

namespace App\Projections\Application\Subscriber\Users;

use App\Projections\Domain\Repository\UserProjectionRepositoryInterface;
use App\Shared\Application\Bus\Event\EventSubscriberInterface;
use App\Shared\Application\Service\UuidGeneratorInterface;
use App\Shared\Domain\Event\Projects\ProjectParticipantWasAddedEvent;
use App\Shared\Domain\Exception\UserNotExistException;

final class ChangeUserProjectionOnParticipantAddedSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly UserProjectionRepositoryInterface $userRepository,
        private readonly UuidGeneratorInterface $uuidGenerator
    ) {
    }

    public function subscribeTo(): array
    {
        return [ProjectParticipantWasAddedEvent::class];
    }

    public function __invoke(ProjectParticipantWasAddedEvent $event): void
    {
        $projection = $this->userRepository->findByUserId($event->participantId);
        if (null === $projection) {
            throw new UserNotExistException($event->participantId);
        }
        $projection = $projection->createCopyForProject(
            $this->uuidGenerator->generate(),
            $event->aggregateId
        );
        $this->userRepository->save($projection);
    }
}
