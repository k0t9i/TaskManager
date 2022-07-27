<?php
declare(strict_types=1);

namespace App\Tasks\Infrastructure\Persistence\Doctrine\Proxy;

use App\Shared\Domain\ValueObject\Users\UserId;

final class TaskManagerParticipantProxyFactory
{
    public function createEntity(TaskManagerParticipantProxy $proxy): UserId
    {
        $entity = new UserId($proxy->getUserId());

        $proxy->changeEntity($entity);

        return $entity;
    }
}
