<?php

declare(strict_types=1);

namespace App\Projects\Application\Service;

use App\Projects\Domain\Entity\Project;
use App\Projects\Domain\Repository\ProjectRepositoryInterface;
use App\Projects\Domain\ValueObject\ProjectTaskId;
use App\Shared\Application\Bus\Event\EventBusInterface;
use App\Shared\Application\Service\UuidGeneratorInterface;
use App\Shared\Domain\Event\DomainEvent;
use App\Shared\Domain\Event\Tasks\TaskWasCreatedEvent;
use App\Shared\Domain\Exception\ProjectNotExistException;
use App\Shared\Domain\ValueObject\Projects\ProjectId;
use App\Shared\Domain\ValueObject\Tasks\TaskId;
use App\Shared\Domain\ValueObject\Users\UserId;

final class ProjectEventHandler
{
    public function __construct(
        private readonly ProjectRepositoryInterface $repository,
        private readonly UuidGeneratorInterface $uuidGenerator,
        private readonly EventBusInterface $eventBus
    ) {
    }

    public function handle(DomainEvent $event): void
    {
        $aggregateRoot = null;
        if ($event instanceof TaskWasCreatedEvent) {
            $aggregateRoot = $this->getProject($event->projectId);
            $this->createTask($aggregateRoot, $event);
        }

        if (null !== $aggregateRoot) {
            $this->repository->save($aggregateRoot);
            $this->eventBus->dispatch(...$aggregateRoot->releaseEvents());
        }
    }

    private function createTask(Project $aggregateRoot, TaskWasCreatedEvent $event): void
    {
        $aggregateRoot->createTask(
            new ProjectTaskId($this->uuidGenerator->generate()),
            new TaskId($event->taskId),
            new UserId($event->ownerId)
        );
    }

    private function getProject(string $projectId): Project
    {
        $aggregateRoot = $this->repository->findById(new ProjectId($projectId));
        if (null === $aggregateRoot) {
            throw new ProjectNotExistException($projectId);
        }
        return $aggregateRoot;
    }
}
