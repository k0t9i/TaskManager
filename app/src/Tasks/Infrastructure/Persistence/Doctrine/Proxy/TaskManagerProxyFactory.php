<?php
declare(strict_types=1);

namespace App\Tasks\Infrastructure\Persistence\Doctrine\Proxy;

use App\Shared\Domain\Collection\UserIdCollection;
use App\Shared\Domain\ValueObject\DateTime;
use App\Shared\Domain\ValueObject\Owner;
use App\Shared\Domain\ValueObject\Participants;
use App\Shared\Domain\ValueObject\Projects\ProjectId;
use App\Shared\Domain\ValueObject\Projects\ProjectStatus;
use App\Shared\Domain\ValueObject\Users\UserId;
use App\Tasks\Domain\Collection\TaskCollection;
use App\Tasks\Domain\Entity\TaskManager;
use App\Tasks\Domain\ValueObject\TaskManagerId;
use App\Tasks\Domain\ValueObject\Tasks;

final class TaskManagerProxyFactory
{
    public function __construct(
        private readonly TaskManagerParticipantProxyFactory $participantProxyFactory,
        private readonly TaskProxyFactory $taskProxyFactory
    ) {
    }

    public function createEntity(?TaskManagerProxy $proxy): ?TaskManager
    {
        if ($proxy === null) {
            return null;
        }

        $participants = new UserIdCollection(array_map(function (TaskManagerParticipantProxy $item) {
            return $this->participantProxyFactory->createEntity($item);
        }, $proxy->getParticipants()->toArray()));
        $tasks = new TaskCollection(array_map(function (TaskProxy $item) {
            return $this->taskProxyFactory->createEntity($item);
        }, $proxy->getTasks()->toArray()));

        $entity = new TaskManager(
            new TaskManagerId($proxy->getId()),
            new ProjectId($proxy->getProjectId()),
            ProjectStatus::createFromScalar($proxy->getStatus()),
            new Owner(
                new UserId($proxy->getOwnerId())
            ),
            DateTime::createFromPhpDateTime($proxy->getFinishDate()),
            new Participants($participants),
            new Tasks($tasks)
        );

        $proxy->changeEntity($entity);
        return $entity;
    }
}
