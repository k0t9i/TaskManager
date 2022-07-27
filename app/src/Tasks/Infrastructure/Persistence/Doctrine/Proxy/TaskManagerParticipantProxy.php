<?php
declare(strict_types=1);

namespace App\Tasks\Infrastructure\Persistence\Doctrine\Proxy;

use App\Shared\Domain\ValueObject\Users\UserId;
use App\Shared\Infrastructure\Persistence\Doctrine\PersistentCollectionLoaderInterface;
use App\Shared\Infrastructure\Persistence\Doctrine\Proxy\DoctrineProxyCollectionItemInterface;
use App\Shared\Infrastructure\Persistence\Doctrine\Proxy\DoctrineProxyInterface;

final class TaskManagerParticipantProxy implements DoctrineProxyCollectionItemInterface, DoctrineProxyInterface
{
    private TaskManagerProxy $manager;
    private string $userId;
    private ?UserId $entity = null;

    public function __construct(TaskManagerProxy $owner, UserId $entity)
    {
        $this->manager = $owner;
        $this->entity = $entity;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function refresh(PersistentCollectionLoaderInterface $loader): void
    {
        $this->userId = $this->entity->value;
    }

    public function changeEntity(UserId $entity): void
    {
        $this->entity = $entity;
    }

    public function getKey(): string
    {
        return $this->userId;
    }
}
