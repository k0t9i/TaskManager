<?php

declare(strict_types=1);

namespace App\Projects\Infrastructure\Persistence\Doctrine\Proxy;

use App\Projects\Domain\Entity\Request;
use App\Projects\Domain\ValueObject\RequestId;
use App\Shared\Domain\ValueObject\DateTime;
use App\Shared\Domain\ValueObject\Requests\RequestStatus;
use App\Shared\Domain\ValueObject\Users\UserId;

final class RequestProxyFactory
{
    public function createEntity(RequestProxy $proxy): Request
    {
        $entity = new Request(
            new RequestId($proxy->getId()),
            new UserId($proxy->getUserId()),
            RequestStatus::createFromScalar($proxy->getStatus()),
            DateTime::createFromPhpDateTime($proxy->getChangeDate())
        );

        $proxy->changeEntity($entity);
        return $entity;
    }
}
