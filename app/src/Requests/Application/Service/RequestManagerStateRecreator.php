<?php
declare(strict_types=1);

namespace App\Requests\Application\Service;

use App\Requests\Domain\DTO\RequestManagerDTO;
use App\Requests\Domain\Entity\RequestManager;
use App\Requests\Domain\Factory\RequestManagerFactory;
use App\Shared\Domain\Bus\Event\DomainEvent;
use App\Shared\Domain\Event\ProjectOwnerWasChangedEvent;
use App\Shared\Domain\Event\ProjectParticipantWasRemovedEvent;
use App\Shared\Domain\Event\ProjectStatusWasChangedEvent;
use App\Shared\Domain\Exception\LogicException;
use App\Shared\Domain\Factory\ProjectStatusFactory;
use App\Shared\Domain\ValueObject\UserId;

final class RequestManagerStateRecreator
{
    public function __construct(
        private readonly RequestManagerFactory $managerFactory
    ) {
    }

    public function fromEvent(RequestManager $source, DomainEvent $event): RequestManager
    {
        if ($event instanceof ProjectStatusWasChangedEvent) {
            return $this->changeStatus($source, $event);
        }
        if ($event instanceof ProjectOwnerWasChangedEvent) {
            return $this->changeOwner($source, $event);
        }
        if ($event instanceof ProjectParticipantWasRemovedEvent) {
            return $this->removeParticipant($source, $event);
        }

        throw new LogicException(sprintf('Invalid domain event "%s"', get_class($event)));
    }

    private function changeStatus(RequestManager $source, ProjectStatusWasChangedEvent $event): RequestManager
    {
        $dto = $this->createManagerDTO($source, [
            'status' => $event->status
        ]);
        return $this->managerFactory->create($dto);
    }

    private function changeOwner(RequestManager $source, ProjectOwnerWasChangedEvent $event): RequestManager
    {
        $dto = $this->createManagerDTO($source, [
            'ownerId' => $event->ownerId
        ]);
        return $this->managerFactory->create($dto);
    }

    private function removeParticipant(RequestManager $source, ProjectParticipantWasRemovedEvent $event): RequestManager
    {
        $participants = $source->getParticipantIds()->remove(new UserId($event->participantId));
        $dto = $this->createManagerDTO($source, [
            'participantIds' => $participants
        ]);

        return $this->managerFactory->create($dto);
    }

    private function createManagerDTO(RequestManager $source, array $attributes): RequestManagerDTO
    {
        return new RequestManagerDTO(
            $attributes['id'] ?? $source->getId()->value,
            $attributes['projectId'] ?? $source->getProjectId()->value,
            (int) $attributes['status'] ?? ProjectStatusFactory::scalarFromObject($source->getStatus()),
            $attributes['ownerId'] ?? $source->getOwnerId()->value,
            $attributes['participantIds'] ?? $source->getParticipantIds(),
            $attributes['requests'] ?? $source->getRequests()
        );
    }
}
