<?php
declare(strict_types=1);

namespace App\Requests\Application\Service;

use App\Requests\Domain\Entity\RequestManager;
use App\Requests\Domain\Exception\RequestManagerNotExistsException;
use App\Requests\Domain\Repository\RequestManagerRepositoryInterface;
use App\Requests\Domain\ValueObject\RequestManagerId;
use App\Requests\Domain\ValueObject\Requests;
use App\Shared\Application\Bus\Event\EventBusInterface;
use App\Shared\Application\Service\UuidGeneratorInterface;
use App\Shared\Domain\Event\DomainEvent;
use App\Shared\Domain\Event\Projects\ProjectOwnerWasChangedEvent;
use App\Shared\Domain\Event\Projects\ProjectParticipantWasAddedEvent;
use App\Shared\Domain\Event\Projects\ProjectParticipantWasRemovedEvent;
use App\Shared\Domain\Event\Projects\ProjectStatusWasChangedEvent;
use App\Shared\Domain\Event\Projects\ProjectWasCreatedEvent;
use App\Shared\Domain\ValueObject\Owner;
use App\Shared\Domain\ValueObject\Participants;
use App\Shared\Domain\ValueObject\Projects\ProjectId;
use App\Shared\Domain\ValueObject\Projects\ProjectStatus;
use App\Shared\Domain\ValueObject\Users\UserId;

final class RequestManagerEventHandler
{
    public function __construct(
        private readonly UuidGeneratorInterface $uuidGenerator,
        private readonly RequestManagerRepositoryInterface $repository,
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

        $this->save($aggregateRoot);
    }

    private function find(DomainEvent $event): RequestManager
    {
        $aggregateRoot = $this->repository->findByProjectId(new ProjectId($event->aggregateId));
        if ($aggregateRoot === null) {
            throw new RequestManagerNotExistsException();
        }
        return $aggregateRoot;
    }

    private function save(?RequestManager $aggregateRoot): void
    {
        if ($aggregateRoot !== null) {
            $this->repository->save($aggregateRoot);
            $this->eventBus->dispatch(...$aggregateRoot->releaseEvents());
        }
    }

    private function create(ProjectWasCreatedEvent $event): RequestManager
    {
        return new RequestManager(
            new RequestManagerId($this->uuidGenerator->generate()),
            new ProjectId($event->aggregateId),
            ProjectStatus::createFromScalar((int) $event->status),
            new Owner(new UserId($event->ownerId)),
            new Participants(),
            new Requests()
        );
    }

    private function changeStatus(RequestManager $aggregateRoot, ProjectStatusWasChangedEvent $event): void
    {
        $status = ProjectStatus::createFromScalar((int) $event->status);
        $aggregateRoot->changeStatus($status);
    }

    private function changeOwner(RequestManager $aggregateRoot, ProjectOwnerWasChangedEvent $event): void
    {
        $aggregateRoot->changeOwner(new Owner(
            new UserId($event->ownerId)
        ));
    }

    private function removeParticipant(RequestManager $aggregateRoot, ProjectParticipantWasRemovedEvent $event): void
    {
        $aggregateRoot->removeParticipant(new UserId($event->participantId));
    }

    private function addParticipant(RequestManager $aggregateRoot, ProjectParticipantWasAddedEvent $event): void
    {
        $aggregateRoot->addParticipant(new UserId($event->participantId));
    }
}
