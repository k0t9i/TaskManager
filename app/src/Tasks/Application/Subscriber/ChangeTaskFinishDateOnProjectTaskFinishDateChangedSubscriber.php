<?php
declare(strict_types=1);

namespace App\Tasks\Application\Subscriber;

use App\Shared\Domain\Bus\Event\DomainEvent;
use App\Shared\Domain\Bus\Event\EventBusInterface;
use App\Shared\Domain\Bus\Event\EventSubscriberInterface;
use App\Shared\Domain\Event\ProjectTaskFinishDateWasChangedEvent;
use App\Shared\Domain\ValueObject\ProjectId;
use App\Tasks\Application\Service\TaskManagerTaskDateChanger;
use App\Tasks\Domain\Exception\TaskManagerNotExistException;
use App\Tasks\Domain\Repository\TaskManagerRepositoryInterface;

final class ChangeTaskFinishDateOnProjectTaskFinishDateChangedSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly TaskManagerRepositoryInterface $managerRepository,
        private readonly TaskManagerTaskDateChanger $taskDateChanger,
        private readonly EventBusInterface $eventBus
    ) {
    }

    /**
     * @return DomainEvent[]
     */
    public function subscribeTo(): array
    {
        return [ProjectTaskFinishDateWasChangedEvent::class];
    }

    public function __invoke(ProjectTaskFinishDateWasChangedEvent $event): void
    {
        $manager = $this->managerRepository->findByProjectId(new ProjectId($event->aggregateId));
        if ($manager === null) {
            throw new TaskManagerNotExistException();
        }

        $newManager = $this->taskDateChanger->changeFinishDate($manager, $event->taskId, $event->finishDate);

        $this->managerRepository->save($newManager);
        $this->eventBus->dispatch(...$newManager->releaseEvents());
    }
}
