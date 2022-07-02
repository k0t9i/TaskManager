<?php
declare(strict_types=1);

namespace App\Tasks\Application\Subscriber;

use App\Projects\Domain\Event\ProjectWasCreatedEvent;
use App\Shared\Domain\Bus\Event\DomainEvent;
use App\Shared\Domain\Bus\Event\EventBusInterface;
use App\Shared\Domain\Bus\Event\EventSubscriberInterface;
use App\Shared\Domain\Collection\UserIdCollection;
use App\Shared\Domain\Factory\ProjectStatusFactory;
use App\Shared\Domain\UuidGeneratorInterface;
use App\Shared\Domain\ValueObject\DateTime;
use App\Shared\Domain\ValueObject\ProjectId;
use App\Shared\Domain\ValueObject\UserId;
use App\Tasks\Domain\Collection\TaskCollection;
use App\Tasks\Domain\Entity\TaskManager;
use App\Tasks\Domain\Repository\TaskManagerRepositoryInterface;
use App\Tasks\Domain\ValueObject\TaskManagerId;

final class CreateOnProjectCreated implements EventSubscriberInterface
{
    public function __construct(
        private readonly TaskManagerRepositoryInterface $managerRepository,
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
        $manager = new TaskManager(
            new TaskManagerId($this->uuidGenerator->generate()),
            new ProjectId($event->aggregateId),
            ProjectStatusFactory::objectFromScalar((int) $event->status),
            new UserId($event->ownerId),
            new DateTime($event->finishDate),
            new UserIdCollection(),
            new TaskCollection()
        );

        $this->managerRepository->save($manager);
        $this->eventBus->dispatch(...$manager->releaseEvents());
    }
}
