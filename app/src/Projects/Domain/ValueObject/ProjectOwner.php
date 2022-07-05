<?php
declare(strict_types=1);

namespace App\Projects\Domain\ValueObject;

use App\Shared\Domain\Collection\Hashable;
use App\Shared\Domain\Exception\UserIsAlreadyOwnerException;
use App\Shared\Domain\Exception\UserIsNotOwnerException;
use App\Shared\Domain\ValueObject\UserId;

final class ProjectOwner implements Hashable
{
    public function __construct(
        public readonly UserId $userId
    ) {
    }

    public function ensureIsOwner(UserId $userId): void
    {
        if (!$this->isOwner($userId)) {
            throw new UserIsNotOwnerException($userId->value);
        }
    }

    public function ensureIsNotOwner(UserId $userId): void
    {
        if ($this->isOwner($userId)) {
            throw new UserIsAlreadyOwnerException($userId->value);
        }
    }

    public function isOwner(UserId $userId): bool
    {
        return $this->userId->isEqual($userId);
    }

    public function getHash(): string
    {
        return $this->userId->getHash();
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

        return $this->userId->isEqual($other->userId);
    }
}
