<?php
declare(strict_types=1);

namespace App\Requests\Infrastructure\Persistence\Doctrine\Proxy;

use App\Shared\Domain\ValueObject\Users\UserId;
use App\Shared\Infrastructure\Persistence\Doctrine\Proxy\DoctrineProxyCollectionItemInterface;

final class RequestManagerParticipantProxy implements DoctrineProxyCollectionItemInterface
{
    private RequestManagerProxy $manager;
    private string $userId;
    private ?UserId $entity = null;

    public function __construct(RequestManagerProxy $parent, UserId $entity)
    {
        $this->manager = $parent;
        $this->entity = $entity;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function refresh(): void
    {
        $this->userId = $this->entity->value;
    }

    public function createEntity(): UserId
    {
        if ($this->entity === null) {
            $this->entity = new UserId($this->userId);
        }
        return $this->entity;
    }

    public function getKey(): string
    {
        return $this->userId;
    }
}
