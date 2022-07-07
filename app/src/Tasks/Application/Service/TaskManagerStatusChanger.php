<?php
declare(strict_types=1);

namespace App\Tasks\Application\Service;

use App\Tasks\Domain\DTO\TaskManagerDTO;
use App\Tasks\Domain\Entity\TaskManager;
use App\Tasks\Domain\Factory\TaskManagerFactory;

final class TaskManagerStatusChanger
{
    public function __construct(private readonly TaskManagerFactory $managerFactory)
    {
    }

    public function changeStatus(TaskManager $taskManager, int $status): TaskManager
    {
        $dto = new TaskManagerDTO(
            $taskManager->getId()->value,
            $taskManager->getProjectId()->value,
            $status,
            $taskManager->getOwner()->userId->value,
            $taskManager->getFinishDate()->getValue(),
            $taskManager->getParticipants()->getInnerItems(),
            $taskManager->getTasks()
        );
        return $this->managerFactory->create($dto);
    }
}
