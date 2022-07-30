<?php

declare(strict_types=1);

namespace App\Tasks\Infrastructure\Persistence\Doctrine\Proxy;

use App\Shared\Domain\ValueObject\Tasks\TaskId;
use App\Tasks\Domain\ValueObject\TaskLink;

final class TaskLinkProxyFactory
{
    public function createEntity(TaskLinkProxy $proxy): TaskLink
    {
        $entity = new TaskLink(
            new TaskId($proxy->getToTaskId())
        );

        $proxy->changeEntity($entity);

        return $entity;
    }
}
