<?php
declare(strict_types=1);

namespace App\Tasks\Application\Service;

use App\Shared\Domain\Factory\TaskStatusFactory;
use App\Tasks\Application\Factory\TaskManagerDTO;
use App\Tasks\Application\Factory\TaskManagerFactory;
use App\Tasks\Domain\Entity\TaskManager;

final class TaskManagerFinishDateChanger
{
    public function __construct(private readonly TaskManagerFactory $managerFactory)
    {
    }

    public function changeOwner(TaskManager $taskManager, string $finishDate): TaskManager
    {
        $dto = new TaskManagerDTO(
            $taskManager->getId()->value,
            $taskManager->getProjectId()->value,
            TaskStatusFactory::scalarFromObject($taskManager->getStatus()),
            $taskManager->getOwnerId()->value,
            $finishDate,
            $taskManager->getParticipantIds(),
            $taskManager->getTasks()
        );
        return $this->managerFactory->create($dto);
    }
}
