<?php
declare(strict_types=1);

namespace App\Tasks\Application\Subscriber;

use App\Projects\Domain\Event\ProjectInformationWasChangedEvent;
use App\Shared\Domain\Bus\Event\DomainEvent;
use App\Shared\Domain\Bus\Event\EventBusInterface;
use App\Shared\Domain\Bus\Event\EventSubscriberInterface;
use App\Shared\Domain\ValueObject\ProjectId;
use App\Tasks\Application\Service\TaskManagerFinishDateChanger;
use App\Tasks\Domain\Exception\TaskManagerNotExistException;
use App\Tasks\Domain\Repository\TaskManagerRepositoryInterface;

final class ChangeFinishDateOnProjectInformationChangedSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly TaskManagerRepositoryInterface $managerRepository,
        private readonly TaskManagerFinishDateChanger $finishDateChanger,
        private readonly EventBusInterface $eventBus
    ) {
    }

    /**
     * @return DomainEvent[]
     */
    public function subscribeTo(): array
    {
        return [ProjectInformationWasChangedEvent::class];
    }

    public function __invoke(ProjectInformationWasChangedEvent $event): void
    {
        $manager = $this->managerRepository->findByProjectId(new ProjectId($event->aggregateId));
        if ($manager === null) {
            throw new TaskManagerNotExistException();
        }

        $newManager = $this->finishDateChanger->changeOwner($manager, $event->finishDate);

        $this->managerRepository->save($newManager);
        $this->eventBus->dispatch(...$newManager->releaseEvents());
    }
}
