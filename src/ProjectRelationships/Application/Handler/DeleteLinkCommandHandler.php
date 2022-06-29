<?php
declare(strict_types=1);

namespace App\ProjectRelationships\Application\Handler;

use App\ProjectRelationships\Application\Command\DeleteLinkCommand;
use App\ProjectRelationships\Domain\Exception\ProjectRelationshipNotExistException;
use App\ProjectRelationships\Domain\Repository\RelationshipRepositoryInterface;
use App\ProjectRelationships\Domain\ValueObject\RelationshipTaskId;
use App\Shared\Domain\Bus\Command\CommandHandlerInterface;
use App\Shared\Domain\Bus\Event\EventBusInterface;
use App\Shared\Domain\ValueObject\UserId;

class DeleteLinkCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly RelationshipRepositoryInterface $relationshipRepository,
        private readonly EventBusInterface $eventBus,
    ) {
    }

    public function __invoke(DeleteLinkCommand $command): void
    {
        $fromTaskId = new RelationshipTaskId($command->fromTaskId);
        $relationship = $this->relationshipRepository->findByTaskId($fromTaskId);
        if ($relationship === null) {
            throw new ProjectRelationshipNotExistException();
        }

        $relationship->deleteLink(
            $fromTaskId,
            new RelationshipTaskId($command->toTaskId),
            new UserId($command->currentUserId)
        );

        $this->relationshipRepository->update($relationship);
        $this->eventBus->dispatch(...$relationship->releaseEvents());
    }
}
