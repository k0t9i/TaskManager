<?php
declare(strict_types=1);

namespace App\Tasks\Application\Subscriber;

use App\Shared\Domain\Bus\Event\DomainEvent;
use App\Shared\Domain\Bus\Event\EventBusInterface;
use App\Shared\Domain\Bus\Event\EventSubscriberInterface;
use App\Shared\Domain\Event\ProjectParticipantWasAddedEvent;
use App\Shared\Domain\ValueObject\ProjectId;
use App\Tasks\Application\Service\TaskManagerParticipantsChanger;
use App\Tasks\Domain\Exception\TaskManagerNotExistException;
use App\Tasks\Domain\Repository\TaskManagerRepositoryInterface;

final class AddParticipantOnProjectParticipantAddedSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly TaskManagerRepositoryInterface $managerRepository,
        private readonly TaskManagerParticipantsChanger $participantsChanger,
        private readonly EventBusInterface $eventBus
    ) {
    }

    /**
     * @return DomainEvent[]
     */
    public function subscribeTo(): array
    {
        return [ProjectParticipantWasAddedEvent::class];
    }

    public function __invoke(ProjectParticipantWasAddedEvent $event): void
    {
        $manager = $this->managerRepository->findByProjectId(new ProjectId($event->projectId));
        if ($manager === null) {
            throw new TaskManagerNotExistException();
        }

        $newManager = $this->participantsChanger->addParticipant($manager, $event->participantId);

        $this->managerRepository->save($newManager);
        $this->eventBus->dispatch(...$newManager->releaseEvents());
    }
}
