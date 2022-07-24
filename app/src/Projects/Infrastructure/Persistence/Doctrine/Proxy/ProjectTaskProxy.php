<?php
declare(strict_types=1);

namespace App\Projects\Infrastructure\Persistence\Doctrine\Proxy;

use App\Projects\Domain\Entity\ProjectTask;
use App\Projects\Domain\ValueObject\ProjectTaskId;
use App\Shared\Domain\Collection\Hashable;
use App\Shared\Domain\ValueObject\Tasks\TaskId;
use App\Shared\Domain\ValueObject\Users\UserId;

final class ProjectTaskProxy implements Hashable
{
    private string $id;
    private string $taskId;
    private string $ownerId;
    private ProjectProxy $project;

    public function getId(): string
    {
        return $this->id;
    }

    public function loadFromEntity(ProjectProxy $project, ProjectTask $entity): void
    {
        $this->id = $entity->getId()->value;
        $this->taskId = $entity->getTaskId()->value;
        $this->ownerId = $entity->getOwnerId()->value;
        $this->project = $project;
    }

    public function createEntity(): ProjectTask
    {
        return new ProjectTask(
            new ProjectTaskId($this->id),
            new TaskId($this->taskId),
            new UserId($this->ownerId),
        );
    }

    public function getHash(): string
    {
        return $this->id;
    }

    public function isEqual(object $other): bool
    {
        if (!($other instanceof Hashable)) {
            return false;
        }
        return $this->getHash() === $other->getHash();
    }
}
