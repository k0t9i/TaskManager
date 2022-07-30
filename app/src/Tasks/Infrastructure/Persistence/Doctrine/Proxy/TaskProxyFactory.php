<?php

declare(strict_types=1);

namespace App\Tasks\Infrastructure\Persistence\Doctrine\Proxy;

use App\Shared\Domain\ValueObject\DateTime;
use App\Shared\Domain\ValueObject\Tasks\TaskId;
use App\Shared\Domain\ValueObject\Users\UserId;
use App\Tasks\Domain\Collection\TaskLinkCollection;
use App\Tasks\Domain\Entity\Task;
use App\Tasks\Domain\ValueObject\TaskBrief;
use App\Tasks\Domain\ValueObject\TaskDescription;
use App\Tasks\Domain\ValueObject\TaskInformation;
use App\Tasks\Domain\ValueObject\TaskName;
use App\Tasks\Domain\ValueObject\TaskStatus;

final class TaskProxyFactory
{
    public function __construct(
        private readonly TaskLinkProxyFactory $taskLinkProxyFactory
    ) {
    }

    public function createEntity(TaskProxy $proxy): Task
    {
        $links = new TaskLinkCollection(array_map(function (TaskLinkProxy $item) {
            return $this->taskLinkProxyFactory->createEntity($item);
        }, $proxy->getLinks()->toArray()));

        $entity = new Task(
            new TaskId($proxy->getId()),
            new TaskInformation(
                new TaskName($proxy->getName()),
                new TaskBrief($proxy->getBrief()),
                new TaskDescription($proxy->getDescription()),
                DateTime::createFromPhpDateTime($proxy->getStartDate()),
                DateTime::createFromPhpDateTime($proxy->getFinishDate())
            ),
            new UserId($proxy->getOwnerId()),
            TaskStatus::createFromScalar($proxy->getStatus()),
            $links
        );

        $proxy->changeEntity($entity);

        return $entity;
    }
}
