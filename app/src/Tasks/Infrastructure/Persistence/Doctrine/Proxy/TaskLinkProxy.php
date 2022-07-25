<?php
declare(strict_types=1);

namespace App\Tasks\Infrastructure\Persistence\Doctrine\Proxy;

use App\Shared\Domain\ValueObject\Tasks\TaskId;
use App\Shared\Infrastructure\Persistence\Doctrine\Proxy\DoctrineProxyCollectionItemInterface;
use App\Tasks\Domain\ValueObject\TaskLink;

final class TaskLinkProxy implements DoctrineProxyCollectionItemInterface
{
    private TaskProxy $task;
    private string $toTaskId;
    private ?TaskLink $entity = null;

    public function __construct(TaskProxy $owner, TaskLink $entity)
    {
        $this->task = $owner;
        $this->entity = $entity;
    }

    public function refresh(): void
    {
        $this->toTaskId = $this->entity->toTaskId->value;
    }

    public function createEntity(): TaskLink
    {
        if ($this->entity === null) {
            $this->entity = new TaskLink(
                new TaskId($this->toTaskId)
            );
        }
        return $this->entity;
    }

    public function getKey(): string
    {
        return $this->toTaskId;
    }
}
