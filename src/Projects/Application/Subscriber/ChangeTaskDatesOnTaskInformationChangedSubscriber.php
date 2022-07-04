<?php
declare(strict_types=1);

namespace App\Projects\Application\Subscriber;

use App\Projects\Application\Service\ProjectTaskDatesChanger;
use App\Projects\Domain\Repository\ProjectRepositoryInterface;
use App\Shared\Domain\Bus\Event\EventBusInterface;
use App\Shared\Domain\Bus\Event\EventSubscriberInterface;
use App\Shared\Domain\Exception\ProjectNotExistException;
use App\Shared\Domain\ValueObject\ProjectId;
use App\Tasks\Domain\Event\TaskInformationWasChangedEvent;

final class ChangeTaskDatesOnTaskInformationChangedSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly ProjectRepositoryInterface $projectRepository,
        private readonly ProjectTaskDatesChanger $datesChanger,
        private readonly EventBusInterface $eventBus
    ) {
    }

    public function subscribeTo(): array
    {
        return [TaskInformationWasChangedEvent::class];
    }

    public function __invoke(TaskInformationWasChangedEvent $event): void
    {
        $project = $this->projectRepository->findById(new ProjectId($event->projectId));
        if ($project === null) {
            throw new ProjectNotExistException($event->projectId);
        }

        $project = $this->datesChanger->changeDates($project, $event->taskId, $event->startDate, $event->finishDate);

        $this->projectRepository->save($project);
        $this->eventBus->dispatch(...$project->releaseEvents());
    }
}
