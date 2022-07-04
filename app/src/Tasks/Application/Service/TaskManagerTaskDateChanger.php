<?php
declare(strict_types=1);

namespace App\Tasks\Application\Service;

use App\Shared\Domain\Factory\ProjectStatusFactory;
use App\Shared\Domain\Factory\TaskStatusFactory;
use App\Tasks\Domain\DTO\TaskDTO;
use App\Tasks\Domain\DTO\TaskManagerDTO;
use App\Tasks\Domain\Entity\Task;
use App\Tasks\Domain\Entity\TaskManager;
use App\Tasks\Domain\Factory\TaskFactory;
use App\Tasks\Domain\Factory\TaskManagerFactory;

final class TaskManagerTaskDateChanger
{
    public function __construct(
        private readonly TaskManagerFactory $managerFactory,
        private readonly TaskFactory $taskFactory,
    ) {
    }

    public function changeStartDate(TaskManager $taskManager, string $taskId, string $startDate): TaskManager
    {
        /** @var Task $task */
        $task = $taskManager->getTasks()->get($taskId);
        //TODO throw exception?
        if ($task === null) {
            return $taskManager;
        }
        return $this->createNewTaskManager(
            $taskManager,
            $task,
            $startDate,
            $task->getInformation()->finishDate->getValue()
        );
    }

    public function changeFinishDate(TaskManager $taskManager, string $taskId, string $finishDate): TaskManager
    {
        /** @var Task $task */
        $task = $taskManager->getTasks()->get($taskId);
        //TODO throw exception?
        if ($task === null) {
            return $taskManager;
        }
        return $this->createNewTaskManager(
            $taskManager,
            $task,
            $task->getInformation()->startDate->getValue(),
            $finishDate
        );
    }

    private function createNewTaskManager(
        TaskManager $taskManager,
        Task $task,
        string $startDate,
        string $finishDate,

    ): TaskManager {
        $taskDto = new TaskDTO(
            $task->getId()->value,
            $task->getInformation()->name->value,
            $task->getInformation()->brief->value,
            $task->getInformation()->description->value,
            $startDate,
            $finishDate,
            $task->getOwnerId()->value,
            TaskStatusFactory::scalarFromObject($task->getStatus()),
            $task->getLinks()
        );
        $tasks = $taskManager->getTasks()->add(
            $this->taskFactory->create($taskDto)
        );

        $dto = new TaskManagerDTO(
            $taskManager->getId()->value,
            $taskManager->getProjectId()->value,
            ProjectStatusFactory::scalarFromObject($taskManager->getStatus()),
            $taskManager->getOwnerId()->value,
            $taskManager->getFinishDate()->getValue(),
            $taskManager->getParticipantIds(),
            $tasks
        );
        return $this->managerFactory->create($dto);
    }
}
