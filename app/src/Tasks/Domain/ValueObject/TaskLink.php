<?php
declare(strict_types=1);

namespace App\Tasks\Domain\ValueObject;

use App\Shared\Domain\Collection\Hashable;
use App\Shared\Domain\ValueObject\Tasks\TaskId;

final class TaskLink implements Hashable
{
    public function __construct(
        public readonly TaskId $toTaskId
    ) {
    }

    public function getHash(): string
    {
        return $this->toTaskId->getHash();
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
}
