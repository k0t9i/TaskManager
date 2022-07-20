<?php
declare(strict_types=1);

namespace App\Projections\Application\Subscriber\Tasks;

use App\Projections\Domain\Repository\TaskProjectionRepositoryInterface;
use App\Shared\Application\Bus\Event\EventSubscriberInterface;
use App\Shared\Domain\Event\DomainEvent;
use App\Shared\Domain\Event\Tasks\TaskLinkWasAddedEvent;
use App\Shared\Domain\Exception\TaskNotExistException;

final class ChangeTaskProjectionOnTaskLinkAddedSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly TaskProjectionRepositoryInterface $projectionRepository,
    ) {
    }

    /**
     * @return DomainEvent[]
     */
    public function subscribeTo(): array
    {
        return [TaskLinkWasAddedEvent::class];
    }

    public function __invoke(TaskLinkWasAddedEvent $event): void
    {
        $projection = $this->projectionRepository->findById($event->fromTaskId);
        if ($projection === null) {
            throw new TaskNotExistException($event->fromTaskId);
        }

        $projection->incrementLinksCount();
        $this->projectionRepository->save($projection);
    }
}
