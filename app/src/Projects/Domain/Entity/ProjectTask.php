<?php
declare(strict_types=1);

namespace App\Projects\Domain\Entity;

use App\Projects\Domain\ValueObject\ProjectTaskId;
use App\Shared\Domain\Collection\Hashable;
use App\Shared\Domain\ValueObject\Tasks\TaskId;
use App\Shared\Domain\ValueObject\Users\UserId;

final class ProjectTask implements Hashable
{
    public function __construct(
        private ProjectTaskId $id,
        private TaskId $taskId,
        private UserId $ownerId
    ) {
    }

    public function getId(): ProjectTaskId
    {
        return $this->id;
    }

    public function getTaskId(): TaskId
    {
        return $this->taskId;
    }

    public function getOwnerId(): UserId
    {
        return $this->ownerId;
    }

    public function getHash(): string
    {
        return $this->getId()->getHash();
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
        return $this->id->isEqual($other->id) &&
            $this->taskId->isEqual($other->taskId) &&
            $this->ownerId->isEqual($other->ownerId);
    }
}
