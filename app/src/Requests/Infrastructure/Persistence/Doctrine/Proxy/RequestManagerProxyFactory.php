<?php
declare(strict_types=1);

namespace App\Requests\Infrastructure\Persistence\Doctrine\Proxy;

use App\Requests\Domain\Collection\RequestCollection;
use App\Requests\Domain\Entity\RequestManager;
use App\Requests\Domain\ValueObject\RequestManagerId;
use App\Requests\Domain\ValueObject\Requests;
use App\Shared\Domain\Collection\UserIdCollection;
use App\Shared\Domain\ValueObject\Owner;
use App\Shared\Domain\ValueObject\Participants;
use App\Shared\Domain\ValueObject\Projects\ProjectId;
use App\Shared\Domain\ValueObject\Projects\ProjectStatus;
use App\Shared\Domain\ValueObject\Users\UserId;

final class RequestManagerProxyFactory
{
    public function __construct(
        private readonly RequestManagerParticipantProxyFactory $participantProxyFactory,
        private readonly RequestProxyFactory $requestProxyFactory
    ) {
    }

    public function createEntity(?RequestManagerProxy $proxy): ?RequestManager
    {
        if ($proxy === null) {
            return null;
        }

        $participants = new UserIdCollection(array_map(function (RequestManagerParticipantProxy $item){
            return $this->participantProxyFactory->createEntity($item);
        }, $proxy->getParticipants()->toArray()));
        $requests = new RequestCollection(array_map(function (RequestProxy $item){
            return $this->requestProxyFactory->createEntity($item);
        }, $proxy->getRequests()->toArray()));

        $entity = new RequestManager(
            new RequestManagerId($proxy->getId()),
            new ProjectId($proxy->getProjectId()),
            ProjectStatus::createFromScalar($proxy->getStatus()),
            new Owner(
                new UserId($proxy->getOwnerId())
            ),
            new Participants($participants),
            new Requests($requests)
        );

        $proxy->changeEntity($entity);
        return $entity;
    }
}
