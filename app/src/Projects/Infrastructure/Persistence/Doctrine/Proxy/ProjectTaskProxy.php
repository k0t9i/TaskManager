<?php
declare(strict_types=1);

namespace App\Projects\Infrastructure\Persistence\Doctrine\Proxy;

use App\Projects\Domain\Entity\ProjectTask;
use App\Shared\Infrastructure\Persistence\Doctrine\PersistentCollectionLoaderInterface;
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

    public function getTaskId(): string
    {
        return $this->taskId;
    }

    public function getOwnerId(): string
    {
        return $this->ownerId;
    }

    public function refresh(PersistentCollectionLoaderInterface $loader): void
    {
        $this->id = $this->entity->getId()->value;
        $this->taskId = $this->entity->getTaskId()->value;
        $this->ownerId = $this->entity->getOwnerId()->value;
    }

    public function changeEntity(ProjectTask $entity): void
    {
        $this->entity = $entity;
    }

    public function getKey(): string
    {
        return $this->id;
    }
}
