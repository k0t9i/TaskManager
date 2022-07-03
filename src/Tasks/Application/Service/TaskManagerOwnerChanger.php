<?php
declare(strict_types=1);

namespace App\Tasks\Application\Service;

use App\Shared\Domain\Factory\TaskStatusFactory;
use App\Tasks\Domain\DTO\TaskManagerDTO;
use App\Tasks\Domain\Entity\TaskManager;
use App\Tasks\Domain\Factory\TaskManagerFactory;

final class TaskManagerOwnerChanger
{
    public function __construct(private readonly TaskManagerFactory $managerFactory)
    {
    }

    public function changeOwner(TaskManager $taskManager, string $ownerId): TaskManager
    {
        $dto = new TaskManagerDTO(
            $taskManager->getId()->value,
            $taskManager->getProjectId()->value,
            TaskStatusFactory::scalarFromObject($taskManager->getStatus()),
            $ownerId,
            $taskManager->getFinishDate()->getValue(),
            $taskManager->getParticipantIds(),
            $taskManager->getTasks()
        );
        return $this->managerFactory->create($dto);
    }
}
