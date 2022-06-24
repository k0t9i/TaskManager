<?php
declare(strict_types=1);

namespace App\ProjectRelationships\Domain\Entity;

use App\ProjectRelationships\Domain\Collection\RelationshipTaskCollection;
use App\ProjectRelationships\Domain\Collection\TaskLinkCollection;
use App\ProjectRelationships\Domain\Event\TaskLinkWasAddedEvent;
use App\ProjectRelationships\Domain\Event\TaskLinkWasDeletedEvent;
use App\ProjectRelationships\Domain\Exception\RelationshipTaskNotExistException;
use App\ProjectRelationships\Domain\Exception\TaskLinkAlreadyExistsException;
use App\ProjectRelationships\Domain\Exception\TaskLinkNotExistException;
use App\ProjectRelationships\Domain\Exception\UserIsNotRelationshipOwnerException;
use App\ProjectRelationships\Domain\ValueObject\RelationshipId;
use App\ProjectRelationships\Domain\ValueObject\RelationshipTaskId;
use App\ProjectRelationships\Domain\ValueObject\TaskLink;
use App\Projects\Domain\ValueObject\ProjectStatus;
use App\Shared\Domain\Aggregate\AggregateRoot;
use App\Shared\Domain\Exception\UserIsNotOwnerException;
use App\Shared\Domain\ValueObject\UserId;

final class Relationship extends AggregateRoot
{
    public function __construct(
        private RelationshipId $id,
        private ProjectStatus $status,
        private UserId $ownerId,
        private RelationshipTaskCollection $tasks,
        private TaskLinkCollection $links
    ) {
    }

    public function createLink(
        RelationshipTaskId $fromTaskId,
        RelationshipTaskId $toTaskId,
        UserId             $currentUserId
    ): void {
        $this->getStatus()->ensureAllowsModification();
        $this->ensureTaskExists($fromTaskId);
        $this->ensureTaskExists($toTaskId);

        $fromTask = $this->getTasks()->get($fromTaskId->getHash());
        $this->ensureCanChangeTaskLink($fromTask, $currentUserId);

        $taskLink = new TaskLink($fromTaskId, $toTaskId);
        $this->ensureLinkDoesNotExist($taskLink);

        $this->getLinks()->add($taskLink);

        $this->registerEvent(new TaskLinkWasAddedEvent(
            $fromTaskId->value,
            $toTaskId->value
        ));
    }

    public function deleteLink(
        RelationshipTaskId $fromTaskId,
        RelationshipTaskId $toTaskId,
        UserId             $currentUserId
    ): void {
        $this->getStatus()->ensureAllowsModification();
        $this->ensureTaskExists($fromTaskId);
        $this->ensureTaskExists($toTaskId);

        $fromTask = $this->getTasks()->get($fromTaskId->getHash());
        $this->ensureCanChangeTaskLink($fromTask, $currentUserId);

        $taskLink = new TaskLink($fromTaskId, $toTaskId);
        $this->ensureLinkExists($taskLink);

        $this->getLinks()->remove($taskLink);

        $this->registerEvent(new TaskLinkWasDeletedEvent(
            $fromTaskId->value,
            $toTaskId->value
        ));
    }

    public function getId(): RelationshipId
    {
        return $this->id;
    }

    public function getStatus(): ProjectStatus
    {
        return $this->status;
    }

    public function getOwnerId(): UserId
    {
        return $this->ownerId;
    }

    /**
     * @return RelationshipTaskCollection|RelationshipTask[]
     */
    public function getTasks(): RelationshipTaskCollection
    {
        return $this->tasks;
    }

    /**
     * @return TaskLinkCollection|TaskLink[]
     */
    public function getLinks(): TaskLinkCollection
    {
        return $this->links;
    }

    private function isOwner(UserId $userId): bool
    {
        return $this->getOwnerId()->isEqual($userId);
    }

    private function isTaskOwner(RelationshipTask $task, UserId $userId): bool
    {
        return $task->getOwnerId()->isEqual($userId);
    }

    private function ensureCanChangeTaskLink(RelationshipTask $task, UserId $userId): void
    {
        if (!$this->isOwner($userId)) {
            throw new UserIsNotOwnerException();
        }
        if (!$this->isTaskOwner($task, $userId)) {
            throw new UserIsNotRelationshipOwnerException();
        }
    }

    private function ensureTaskExists(RelationshipTaskId $taskId): void
    {
        if (!$this->getTasks()->hashExists($taskId->getHash())) {
            throw new RelationshipTaskNotExistException();
        }
    }

    private function ensureLinkDoesNotExist(TaskLink $link): void
    {
        if ($this->getLinks()->exists($link)) {
            throw new TaskLinkAlreadyExistsException();
        }
    }

    private function ensureLinkExists(TaskLink $link): void
    {
        if (!$this->getLinks()->exists($link)) {
            throw new TaskLinkNotExistException();
        }
    }
}