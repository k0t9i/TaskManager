<?php
declare(strict_types=1);

namespace App\Requests\Application\Service;

use App\Requests\Domain\DTO\RequestManagerMergeDTO;
use App\Requests\Domain\Entity\RequestManager;
use App\Requests\Domain\Factory\RequestManagerMerger;
use App\Shared\Domain\Bus\Event\DomainEvent;
use App\Shared\Domain\Event\ProjectOwnerWasChangedEvent;
use App\Shared\Domain\Event\ProjectParticipantWasRemovedEvent;
use App\Shared\Domain\Event\ProjectStatusWasChangedEvent;
use App\Shared\Domain\Exception\LogicException;
use App\Shared\Domain\ValueObject\UserId;

final class RequestManagerStateRecreator
{
    public function __construct(
        private readonly RequestManagerMerger $managerMerger
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
        return $this->managerMerger->merge($source, new RequestManagerMergeDTO(
            status: (int) $event->status
        ));
    }

    private function changeOwner(RequestManager $source, ProjectOwnerWasChangedEvent $event): RequestManager
    {
        return $this->managerMerger->merge($source, new RequestManagerMergeDTO(
            ownerId: $event->ownerId
        ));
    }

    private function removeParticipant(RequestManager $source, ProjectParticipantWasRemovedEvent $event): RequestManager
    {
        $participants = $source->getParticipants()->remove(new UserId($event->participantId));

        return $this->managerMerger->merge($source, new RequestManagerMergeDTO(
            participantIds: $participants->getInnerItems()
        ));
    }
}
