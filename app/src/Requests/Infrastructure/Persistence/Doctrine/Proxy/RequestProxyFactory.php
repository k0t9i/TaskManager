<?php
declare(strict_types=1);

namespace App\Requests\Infrastructure\Persistence\Doctrine\Proxy;

use App\Requests\Domain\Entity\Request;
use App\Requests\Domain\ValueObject\RequestId;
use App\Shared\Domain\ValueObject\DateTime;
use App\Shared\Domain\ValueObject\Requests\RequestStatus;
use App\Shared\Domain\ValueObject\Users\UserId;

final class RequestProxyFactory
{
    public function createEntity(RequestProxy $proxy): Request
    {
        $entity = new Request(
            new RequestId($proxy->getId()),
            new UserId($proxy->getId()),
            RequestStatus::createFromScalar($proxy->getStatus()),
            DateTime::createFromPhpDateTime($proxy->getChangeDate())
        );

        $proxy->changeEntity($entity);
        return $entity;
    }
}
