<?php
declare(strict_types=1);

namespace App\Tasks\Application\Service;

use App\Shared\Domain\Factory\ProjectStatusFactory;
use App\Tasks\Domain\DTO\TaskManagerDTO;
use App\Tasks\Domain\Entity\TaskManager;
use App\Tasks\Domain\Factory\TaskManagerFactory;

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
            ProjectStatusFactory::scalarFromObject($taskManager->getStatus()),
            $taskManager->getOwnerId()->value,
            $finishDate,
            $taskManager->getParticipantIds(),
            $taskManager->getTasks()
        );
        return $this->managerFactory->create($dto);
    }
}
