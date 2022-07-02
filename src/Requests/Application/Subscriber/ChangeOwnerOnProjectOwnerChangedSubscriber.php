<?php
declare(strict_types=1);

namespace App\Requests\Application\Subscriber;

use App\Projects\Domain\Event\ProjectOwnerWasChangedEvent;
use App\Requests\Application\Service\RequestManagerOwnerChanger;
use App\Requests\Domain\Exception\RequestManagerNotExistsException;
use App\Requests\Domain\Repository\RequestManagerRepositoryInterface;
use App\Shared\Domain\Bus\Event\DomainEvent;
use App\Shared\Domain\Bus\Event\EventBusInterface;
use App\Shared\Domain\Bus\Event\EventSubscriberInterface;
use App\Shared\Domain\ValueObject\ProjectId;

final class ChangeOwnerOnProjectOwnerChangedSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly RequestManagerRepositoryInterface $managerRepository,
        private readonly RequestManagerOwnerChanger $managerOwnerChanger,
        private readonly EventBusInterface $eventBus
    ) {
    }

    /**
     * @return DomainEvent[]
     */
    public function subscribeTo(): array
    {
        return [ProjectOwnerWasChangedEvent::class];
    }

    public function __invoke(ProjectOwnerWasChangedEvent $event): void
    {
        $manager = $this->managerRepository->findByProjectId(new ProjectId($event->aggregateId));
        if ($manager === null) {
            throw new RequestManagerNotExistsException();
        }

        $newManager = $this->managerOwnerChanger->changeOwner($manager, $event->ownerId);

        $this->managerRepository->save($newManager);
        $this->eventBus->dispatch(...$newManager->releaseEvents());
    }
}
