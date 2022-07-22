<?php
declare(strict_types=1);

namespace App\Tasks\Application\Service;

use App\Shared\Application\Bus\Event\EventBusInterface;
use App\Shared\Application\Service\UuidGeneratorInterface;
use App\Shared\Domain\Event\DomainEvent;
use App\Shared\Domain\Event\Projects\ProjectInformationWasChangedEvent;
use App\Shared\Domain\Event\Projects\ProjectOwnerWasChangedEvent;
use App\Shared\Domain\Event\Projects\ProjectParticipantWasAddedEvent;
use App\Shared\Domain\Event\Projects\ProjectParticipantWasRemovedEvent;
use App\Shared\Domain\Event\Projects\ProjectStatusWasChangedEvent;
use App\Shared\Domain\Event\Projects\ProjectWasCreatedEvent;
use App\Shared\Domain\ValueObject\DateTime;
use App\Shared\Domain\ValueObject\Owner;
use App\Shared\Domain\ValueObject\Participants;
use App\Shared\Domain\ValueObject\Projects\ProjectId;
use App\Shared\Domain\ValueObject\Projects\ProjectStatus;
use App\Shared\Domain\ValueObject\Users\UserId;
use App\Tasks\Domain\Entity\TaskManager;
use App\Tasks\Domain\Exception\TaskManagerNotExistException;
use App\Tasks\Domain\Repository\TaskManagerRepositoryInterface;
use App\Tasks\Domain\ValueObject\TaskManagerId;
use App\Tasks\Domain\ValueObject\Tasks;

final class TaskManagerEventHandler
{
    public function __construct(
        private readonly UuidGeneratorInterface $uuidGenerator,
        private readonly TaskManagerRepositoryInterface $repository,
        private readonly EventBusInterface $eventBus
    ) {
    }

    public function handle(DomainEvent $event): void
    {
        $aggregateRoot = null;

        if ($event instanceof ProjectWasCreatedEvent) {
            $aggregateRoot = $this->create($event);
        }
        if ($event instanceof ProjectStatusWasChangedEvent) {
            $aggregateRoot = $this->find($event);
            $this->changeStatus($aggregateRoot, $event);
        }
        if ($event instanceof ProjectOwnerWasChangedEvent) {
            $aggregateRoot = $this->find($event);
            $this->changeOwner($aggregateRoot, $event);
        }
        if ($event instanceof ProjectParticipantWasRemovedEvent) {
            $aggregateRoot = $this->find($event);
            $this->removeParticipant($aggregateRoot, $event);
        }
        if ($event instanceof ProjectParticipantWasAddedEvent) {
            $aggregateRoot = $this->find($event);
            $this->addParticipant($aggregateRoot, $event);
        }
        if ($event instanceof ProjectInformationWasChangedEvent) {
            $aggregateRoot = $this->find($event);
            $this->changeFinishDate($aggregateRoot, $event);
        }

        $this->save($aggregateRoot);
    }

    private function find(DomainEvent $event): TaskManager
    {
        $aggregateRoot = $this->repository->findByProjectId(new ProjectId($event->aggregateId));
        if ($aggregateRoot === null) {
            throw new TaskManagerNotExistException();
        }
        return $aggregateRoot;
    }

    private function save(?TaskManager $aggregateRoot): void
    {
        if ($aggregateRoot !== null) {
            $this->repository->save($aggregateRoot);
            $this->eventBus->dispatch(...$aggregateRoot->releaseEvents());
        }
    }

    private function create(ProjectWasCreatedEvent $event): TaskManager
    {
        return new TaskManager(
            new TaskManagerId($this->uuidGenerator->generate()),
            new ProjectId($event->aggregateId),
            ProjectStatus::createFromScalar((int) $event->status),
            new Owner(new UserId($event->ownerId)),
            new DateTime($event->finishDate),
            new Participants(),
            new Tasks()
        );
    }

    private function changeStatus(TaskManager $aggregateRoot, ProjectStatusWasChangedEvent $event): void
    {
        $status = ProjectStatus::createFromScalar((int) $event->status);
        $aggregateRoot->changeStatus($status);
    }

    private function changeOwner(TaskManager $aggregateRoot, ProjectOwnerWasChangedEvent $event): void
    {
        $aggregateRoot->changeOwner(new Owner(
            new UserId($event->ownerId)
        ));
    }

    private function removeParticipant(TaskManager $aggregateRoot, ProjectParticipantWasRemovedEvent $event): void
    {
        $aggregateRoot->removeParticipant(new UserId($event->participantId));
    }

    private function addParticipant(TaskManager $aggregateRoot, ProjectParticipantWasAddedEvent $event): void
    {
        $aggregateRoot->addParticipant(new UserId($event->participantId));
    }

    private function changeFinishDate(TaskManager $aggregateRoot, ProjectInformationWasChangedEvent $event): void
    {
        $aggregateRoot->changeFinishDate(new DateTime($event->finishDate));
    }
}
