<?php
declare(strict_types=1);

namespace App\Requests\Infrastructure\Persistence\Doctrine\Proxy;

use App\Shared\Domain\ValueObject\Users\UserId;

final class RequestManagerParticipantProxyFactory
{
    public function createEntity(RequestManagerParticipantProxy $proxy): UserId
    {
        $entity = new UserId($proxy->getUserId());

        $proxy->changeEntity($entity);

        return $entity;
    }
}
