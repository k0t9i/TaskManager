<?php

declare(strict_types=1);

namespace App\Projections\Application\Subscriber\TaskLinks;

use App\Projections\Domain\Entity\TaskLinkProjection;
use App\Projections\Domain\Repository\TaskLinkProjectionRepositoryInterface;
use App\Shared\Application\Bus\Event\EventSubscriberInterface;
use App\Shared\Domain\Event\Tasks\TaskLinkWasAddedEvent;
use Exception;

final class CreateOnTaskLinkAdded implements EventSubscriberInterface
{
    public function __construct(
        private readonly TaskLinkProjectionRepositoryInterface $repository
    ) {
    }

    public function subscribeTo(): array
    {
        return [TaskLinkWasAddedEvent::class];
    }

    /**
     * @throws Exception
     */
    public function __invoke(TaskLinkWasAddedEvent $event): void
    {
        $projection = new TaskLinkProjection(
            $event->fromTaskId,
            $event->toTaskId
        );

        $this->repository->save($projection);
    }
}
