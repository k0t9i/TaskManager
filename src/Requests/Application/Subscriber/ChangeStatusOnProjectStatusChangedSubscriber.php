<?php
declare(strict_types=1);

namespace App\Requests\Application\Subscriber;

use App\Projects\Domain\Event\ProjectStatusWasChangedEvent;
use App\Requests\Application\Factory\RequestManagerStatusChanger;
use App\Requests\Domain\Exception\RequestManagerNotExistsException;
use App\Requests\Domain\Repository\RequestManagerRepositoryInterface;
use App\Shared\Domain\Bus\Event\DomainEvent;
use App\Shared\Domain\Bus\Event\EventBusInterface;
use App\Shared\Domain\Bus\Event\EventSubscriberInterface;
use App\Shared\Domain\ValueObject\ProjectId;

final class ChangeStatusOnProjectStatusChangedSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly RequestManagerRepositoryInterface $managerRepository,
        private readonly RequestManagerStatusChanger $managerStatusChanger,
        private readonly EventBusInterface $eventBus
    ) {
    }

    /**
     * @return DomainEvent[]
     */
    public function subscribeTo(): array
    {
        return [ProjectStatusWasChangedEvent::class];
    }

    public function __invoke(ProjectStatusWasChangedEvent $event): void
    {
        $manager = $this->managerRepository->findByProjectId(new ProjectId($event->aggregateId));
        if ($manager === null) {
            throw new RequestManagerNotExistsException();
        }

        $newManager = $this->managerStatusChanger->changeStatus($manager, (int) $event->status);

        $this->managerRepository->save($newManager);
        $this->eventBus->dispatch(...$newManager->releaseEvents());
    }
}
