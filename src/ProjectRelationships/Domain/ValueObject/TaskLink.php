<?php
declare(strict_types=1);

namespace App\ProjectRelationships\Domain\ValueObject;

use App\ProjectRelationships\Domain\Exception\TasksOfProjectRelationshipTaskLinkAreEqualException;
use App\Shared\Domain\Collection\Hashable;

final class TaskLink implements Hashable
{
    public function __construct(
        public readonly RelationshipTaskId $fromTaskId, //TODO same as ProjectId
        public readonly RelationshipTaskId $toTaskId //TODO same as ProjectId
    ) {
        $this->ensureIsDifferentTasks();
    }

    public function getHash(): string
    {
        //The link has no direction, so both hashes are equal
        //Sort them, then the link (a,b) will be equal to the link (b,a)
        $hashes = [$this->fromTaskId->getHash(), $this->toTaskId->getHash()];
        sort($hashes);
        return implode('|', $hashes);
    }

    private function ensureIsDifferentTasks()
    {
        if ($this->fromTaskId->isEqual($this->toTaskId)) {
            throw new TasksOfProjectRelationshipTaskLinkAreEqualException();
        }
    }
}
