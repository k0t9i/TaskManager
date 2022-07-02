<?php
declare(strict_types=1);

namespace App\Requests\Application\Subscriber;

use App\Projects\Domain\Event\ProjectWasCreatedEvent;
use App\Requests\Application\Factory\RequestManagerCreator;
use App\Requests\Domain\Repository\RequestManagerRepositoryInterface;
use App\Shared\Domain\Bus\Event\DomainEvent;
use App\Shared\Domain\Bus\Event\EventBusInterface;
use App\Shared\Domain\Bus\Event\EventSubscriberInterface;

final class CreateOnProjectCreated implements EventSubscriberInterface
{
    public function __construct(
        private readonly RequestManagerRepositoryInterface $managerRepository,
        private readonly RequestManagerCreator $managerCreator,
        private readonly EventBusInterface $eventBus
    ) {
    }

    /**
     * @return DomainEvent[]
     */
    public function subscribeTo(): array
    {
        return [ProjectWasCreatedEvent::class];
    }

    public function __invoke(ProjectWasCreatedEvent $event): void
    {
        $manager = $this->managerCreator->create($event->aggregateId, (int) $event->status, $event->ownerId);

        $this->managerRepository->save($manager);
        $this->eventBus->dispatch(...$manager->releaseEvents());
    }
}
