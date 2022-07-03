<?php
declare(strict_types=1);

namespace App\Tasks\Domain\ValueObject;

use App\Shared\Domain\Collection\Hashable;
use App\Shared\Domain\ValueObject\TaskId;
use App\Tasks\Domain\Exception\TasksOfTaskLinkAreEqualException;

final class TaskLink implements Hashable
{
    public function __construct(
        public readonly TaskId $fromTaskId,
        public readonly TaskId $toTaskId
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

    /**
     * @param self $other
     * @return bool
     */
    public function isEqual(object $other): bool
    {
        if (get_class($this) !== get_class($other)) {
            return false;
        }
        return $this->getHash() === $other->getHash();
    }

    private function ensureIsDifferentTasks()
    {
        if ($this->fromTaskId->isEqual($this->toTaskId)) {
            throw new TasksOfTaskLinkAreEqualException();
        }
    }
}
