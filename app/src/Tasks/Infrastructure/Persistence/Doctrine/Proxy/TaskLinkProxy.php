<?php
declare(strict_types=1);

namespace App\Tasks\Infrastructure\Persistence\Doctrine\Proxy;

use App\Shared\Infrastructure\Persistence\Doctrine\PersistentCollectionLoaderInterface;
use App\Shared\Infrastructure\Persistence\Doctrine\Proxy\DoctrineProxyCollectionItemInterface;
use App\Shared\Infrastructure\Persistence\Doctrine\Proxy\DoctrineProxyInterface;
use App\Tasks\Domain\ValueObject\TaskLink;

final class TaskLinkProxy implements DoctrineProxyCollectionItemInterface, DoctrineProxyInterface
{
    private TaskProxy $task;
    private string $toTaskId;
    private ?TaskLink $entity = null;

    public function __construct(TaskProxy $owner, TaskLink $entity)
    {
        $this->task = $owner;
        $this->entity = $entity;
    }

    public function getToTaskId(): string
    {
        return $this->toTaskId;
    }

    public function refresh(PersistentCollectionLoaderInterface $loader): void
    {
        $this->toTaskId = $this->entity->toTaskId->value;
    }

    public function changeEntity(TaskLink $entity): void
    {
        $this->entity = $entity;
    }

    public function getKey(): string
    {
        return $this->toTaskId;
    }
}
