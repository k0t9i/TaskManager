<?php
declare(strict_types=1);

namespace App\Tasks\Application\Service;

use App\Shared\Domain\Factory\TaskStatusFactory;
use App\Tasks\Application\Factory\TaskDTO;
use App\Tasks\Application\Factory\TaskFactory;
use App\Tasks\Application\Factory\TaskManagerDTO;
use App\Tasks\Application\Factory\TaskManagerFactory;
use App\Tasks\Domain\Entity\Task;
use App\Tasks\Domain\Entity\TaskManager;

final class TaskManagerTaskStatusChanger
{
    public function __construct(
        private readonly TaskManagerFactory $managerFactory,
        private readonly TaskFactory $taskFactory,
    ) {
    }

    public function changeStatus(TaskManager $taskManager, string $taskId, int $status): TaskManager
    {
        /** @var Task $task */
        $task = $taskManager->getTasks()->get($taskId);
        //TODO throw exception?
        if ($task === null) {
            return $taskManager;
        }

        $taskDto = new TaskDTO(
            $task->getId()->value,
            $task->getInformation()->name->value,
            $task->getInformation()->brief->value,
            $task->getInformation()->description->value,
            $task->getInformation()->startDate->getValue(),
            $task->getInformation()->finishDate->getValue(),
            $task->getOwnerId()->value,
            $status,
            $task->getLinks()
        );
        $tasks = $taskManager->getTasks()->add(
            $this->taskFactory->create($taskDto)
        );

        $dto = new TaskManagerDTO(
            $taskManager->getId()->value,
            $taskManager->getProjectId()->value,
            TaskStatusFactory::scalarFromObject($taskManager->getStatus()),
            $taskManager->getOwnerId()->value,
            $taskManager->getFinishDate()->getValue(),
            $taskManager->getParticipantIds(),
            $tasks
        );
        return $this->managerFactory->create($dto);
    }
}
