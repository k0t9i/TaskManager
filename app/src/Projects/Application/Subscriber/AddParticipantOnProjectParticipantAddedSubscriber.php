<?php
declare(strict_types=1);

namespace App\Projects\Application\Subscriber;

use App\Projects\Application\Service\ProjectParticipantAdder;
use App\Projects\Domain\Repository\ProjectRepositoryInterface;
use App\Shared\Domain\Bus\Event\EventBusInterface;
use App\Shared\Domain\Bus\Event\EventSubscriberInterface;
use App\Shared\Domain\Event\ProjectParticipantWasAddedEvent;
use App\Shared\Domain\Exception\ProjectNotExistException;
use App\Shared\Domain\ValueObject\ProjectId;

final class AddParticipantOnProjectParticipantAddedSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly ProjectRepositoryInterface $projectRepository,
        private readonly ProjectParticipantAdder $participantAdder,
        private readonly EventBusInterface $eventBus
    ) {
    }

    public function subscribeTo(): array
    {
        return [ProjectParticipantWasAddedEvent::class];
    }

    public function __invoke(ProjectParticipantWasAddedEvent $event): void
    {
        $project = $this->projectRepository->findById(new ProjectId($event->projectId));
        if ($project === null) {
            throw new ProjectNotExistException($event->projectId);
        }

        $project = $this->participantAdder->addParticipant($project, $event->participantId);

        $this->projectRepository->save($project);
        $this->eventBus->dispatch(...$project->releaseEvents());
    }
}
