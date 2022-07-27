<?php
declare(strict_types=1);

namespace App\Projects\Infrastructure\Persistence\Doctrine\Proxy;

use App\Projects\Domain\Collection\ProjectTaskCollection;
use App\Projects\Domain\Entity\Project;
use App\Projects\Domain\ValueObject\ProjectDescription;
use App\Projects\Domain\ValueObject\ProjectInformation;
use App\Projects\Domain\ValueObject\ProjectName;
use App\Projects\Domain\ValueObject\ProjectTasks;
use App\Shared\Domain\Collection\UserIdCollection;
use App\Shared\Domain\ValueObject\DateTime;
use App\Shared\Domain\ValueObject\Owner;
use App\Shared\Domain\ValueObject\Participants;
use App\Shared\Domain\ValueObject\Projects\ProjectId;
use App\Shared\Domain\ValueObject\Projects\ProjectStatus;
use App\Shared\Domain\ValueObject\Users\UserId;

final class ProjectProxyFactory
{
    public function __construct(
        private readonly ProjectParticipantProxyFactory $participantProxyFactory,
        private readonly ProjectTaskProxyFactory $taskProxyFactory,
    ) {
    }

    public function createEntity(?ProjectProxy $proxy): ?Project
    {
        if ($proxy === null) {
            return null;
        }

        $participants = new UserIdCollection(array_map(function (ProjectParticipantProxy $item){
            return $this->participantProxyFactory->createEntity($item);
        }, $proxy->getParticipants()->toArray()));
        $tasks = new ProjectTaskCollection(array_map(function (ProjectTaskProxy $item){
            return $this->taskProxyFactory->createEntity($item);
        }, $proxy->getTasks()->toArray()));

        $entity = new Project(
            new ProjectId($proxy->getId()),
            new ProjectInformation(
                new ProjectName($proxy->getName()),
                new ProjectDescription($proxy->getDescription()),
                DateTime::createFromPhpDateTime($proxy->getFinishDate())
            ),
            ProjectStatus::createFromScalar($proxy->getStatus()),
            new Owner(
                new UserId($proxy->getOwnerId())
            ),
            new Participants($participants),
            new ProjectTasks($tasks)
        );

        $proxy->changeEntity($entity);

        return $entity;
    }
}
