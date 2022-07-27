<?php
declare(strict_types=1);

namespace App\Projects\Infrastructure\Persistence\Doctrine\Proxy;

use App\Projects\Domain\Entity\ProjectTask;
use App\Projects\Domain\ValueObject\ProjectTaskId;
use App\Shared\Domain\ValueObject\Tasks\TaskId;
use App\Shared\Domain\ValueObject\Users\UserId;
use App\Shared\Infrastructure\Persistence\Doctrine\Proxy\DoctrineProxyCollectionItemInterface;
use App\Shared\Infrastructure\Persistence\Doctrine\Proxy\DoctrineProxyInterface;

final class ProjectTaskProxy implements DoctrineProxyCollectionItemInterface, DoctrineProxyInterface
{
    private string $id;
    private string $taskId;
    private string $ownerId;
    private ProjectProxy $project;
    private ?ProjectTask $entity = null;

    public function __construct(ProjectProxy $owner, ProjectTask $entity)
    {
        $this->project = $owner;
        $this->entity = $entity;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function refresh(): void
    {
        $this->id = $this->entity->getId()->value;
        $this->taskId = $this->entity->getTaskId()->value;
        $this->ownerId = $this->entity->getOwnerId()->value;
    }

    public function createEntity(): ProjectTask
    {
        if ($this->entity === null) {
            $this->entity = new ProjectTask(
                new ProjectTaskId($this->id),
                new TaskId($this->taskId),
                new UserId($this->ownerId),
            );
        }
        return $this->entity;
    }

    public function getKey(): string
    {
        return $this->id;
    }
}
