<?php
declare(strict_types=1);

namespace App\Requests\Application\Subscriber;

use App\Requests\Domain\Collection\RequestCollection;
use App\Requests\Domain\DTO\RequestManagerDTO;
use App\Requests\Domain\Factory\RequestManagerFactory;
use App\Requests\Domain\Repository\RequestManagerRepositoryInterface;
use App\Shared\Application\Bus\Event\DomainEvent;
use App\Shared\Application\Bus\Event\EventBusInterface;
use App\Shared\Application\Bus\Event\EventSubscriberInterface;
use App\Shared\Domain\Collection\UserIdCollection;
use App\Shared\Domain\Event\Projects\ProjectWasCreatedEvent;
use App\Shared\Domain\Service\UuidGeneratorInterface;

final class CreateOnProjectCreatedSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly RequestManagerRepositoryInterface $managerRepository,
        private readonly RequestManagerFactory $managerFactory,
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
        $manager = $this->managerFactory->create(new RequestManagerDTO(
            $this->uuidGenerator->generate(),
            $event->aggregateId,
            (int) $event->status,
            $event->ownerId,
            new UserIdCollection(),
            new RequestCollection()
        ));

        $this->managerRepository->save($manager);
        $this->eventBus->dispatch(...$manager->releaseEvents());
    }
}
