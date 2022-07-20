<?php
declare(strict_types=1);

namespace App\Tasks\Application\Subscriber;

use App\Shared\Application\Bus\Event\EventBusInterface;
use App\Shared\Application\Bus\Event\EventSubscriberInterface;
use App\Shared\Application\Service\UuidGeneratorInterface;
use App\Shared\Domain\Collection\UserIdCollection;
use App\Shared\Domain\Event\DomainEvent;
use App\Shared\Domain\Event\Projects\ProjectWasCreatedEvent;
use App\Tasks\Application\DTO\TaskManagerDTO;
use App\Tasks\Application\Factory\TaskManagerFactory;
use App\Tasks\Domain\Collection\TaskCollection;
use App\Tasks\Domain\Repository\TaskManagerRepositoryInterface;

final class CreateOnProjectCreated implements EventSubscriberInterface
{
    public function __construct(
        private readonly TaskManagerRepositoryInterface $managerRepository,
        private readonly TaskManagerFactory $managerFactory,
        private readonly UuidGeneratorInterface $uuidGenerator,
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
        $manager = $this->managerFactory->create(new TaskManagerDTO(
            $this->uuidGenerator->generate(),
            $event->aggregateId,
            (int) $event->status,
            $event->ownerId,
            $event->finishDate,
            new UserIdCollection(),
            new TaskCollection()
        ));

        $this->managerRepository->save($manager);
        $this->eventBus->dispatch(...$manager->releaseEvents());
    }
}
