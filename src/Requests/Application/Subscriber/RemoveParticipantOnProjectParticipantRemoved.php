<?php
declare(strict_types=1);

namespace App\Requests\Application\Subscriber;

use App\Projects\Domain\Event\ProjectParticipantWasRemovedEvent;
use App\Requests\Application\Factory\RequestManagerParticipantRemover;
use App\Requests\Domain\Exception\RequestManagerNotExistsException;
use App\Requests\Domain\Repository\RequestManagerRepositoryInterface;
use App\Shared\Domain\Bus\Event\DomainEvent;
use App\Shared\Domain\Bus\Event\EventBusInterface;
use App\Shared\Domain\Bus\Event\EventSubscriberInterface;
use App\Shared\Domain\ValueObject\ProjectId;

final class RemoveParticipantOnProjectParticipantRemoved implements EventSubscriberInterface
{
    public function __construct(
        private readonly RequestManagerRepositoryInterface $managerRepository,
        private readonly RequestManagerParticipantRemover $managerParticipantRemover,
        private readonly EventBusInterface $eventBus
    ) {
    }

    /**
     * @return DomainEvent[]
     */
    public function subscribeTo(): array
    {
        return [ProjectParticipantWasRemovedEvent::class];
    }

    public function __invoke(ProjectParticipantWasRemovedEvent $event): void
    {
        $manager = $this->managerRepository->findByProjectId(new ProjectId($event->aggregateId));
        if ($manager === null) {
            throw new RequestManagerNotExistsException();
        }

        $newManager = $this->managerParticipantRemover->removeParticipant($manager, $event->participantId);

        $this->managerRepository->save($newManager);
        $this->eventBus->dispatch(...$newManager->releaseEvents());
    }
}
