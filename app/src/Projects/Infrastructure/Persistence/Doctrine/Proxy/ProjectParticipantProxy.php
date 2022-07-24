<?php
declare(strict_types=1);

namespace App\Projects\Infrastructure\Persistence\Doctrine\Proxy;

use App\Shared\Domain\Collection\Hashable;
use App\Shared\Domain\ValueObject\Users\UserId;

final class ProjectParticipantProxy implements Hashable
{
    private ProjectProxy $project;
    private string $userId;

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function loadFromEntity(ProjectProxy $project, UserId $entity): void
    {
        $this->userId = $entity->value;
        $this->project = $project;
    }

    public function createEntity(): UserId
    {
        return new UserId($this->userId);
    }

    public function getHash(): string
    {
        return $this->userId;
    }

    public function isEqual(object $other): bool
    {
        if (!($other instanceof Hashable)) {
            return false;
        }
        return $this->getHash() === $other->getHash();
    }
}
