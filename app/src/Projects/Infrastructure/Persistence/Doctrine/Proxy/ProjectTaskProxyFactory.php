<?php
declare(strict_types=1);

namespace App\Projects\Infrastructure\Persistence\Doctrine\Proxy;

use App\Projects\Domain\Entity\ProjectTask;
use App\Projects\Domain\ValueObject\ProjectTaskId;
use App\Shared\Domain\ValueObject\Tasks\TaskId;
use App\Shared\Domain\ValueObject\Users\UserId;

final class ProjectTaskProxyFactory
{
    public function createEntity(ProjectTaskProxy $proxy): ProjectTask
    {
        $entity = new ProjectTask(
            new ProjectTaskId($proxy->getId()),
            new TaskId($proxy->getTaskId()),
            new UserId($proxy->getOwnerId()),
        );

        $proxy->changeEntity($entity);

        return $entity;
    }
}
