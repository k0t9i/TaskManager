<?php
declare(strict_types=1);

namespace App\Projections\Application\Subscriber\TaskLinks;

use App\Projections\Domain\Repository\TaskLinkProjectionRepositoryInterface;
use App\Shared\Application\Bus\Event\EventSubscriberInterface;
use App\Shared\Domain\Event\DomainEvent;
use App\Shared\Domain\Event\Tasks\TaskLinkWasDeletedEvent;
use App\Tasks\Domain\Exception\TaskLinkNotExistException;
use Exception;

final class DeleteOnTaskLinkDeleted implements EventSubscriberInterface
{
    public function __construct(
        private readonly TaskLinkProjectionRepositoryInterface $repository
    ) {
    }

    /**
     * @return DomainEvent[]
     */
    public function subscribeTo(): array
    {
        return [TaskLinkWasDeletedEvent::class];
    }

    /**
     * @throws Exception
     */
    public function __invoke(TaskLinkWasDeletedEvent $event): void
    {
        $projection = $this->repository->findById($event->fromTaskId, $event->toTaskId);
        if ($projection === null) {
            throw new TaskLinkNotExistException($event->fromTaskId, $event->toTaskId);
        }

        $this->repository->delete($projection);
    }
}
