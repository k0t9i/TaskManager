<?php
declare(strict_types=1);

namespace App\Tasks\Application\Service;

use App\Shared\Domain\Factory\ProjectStatusFactory;
use App\Shared\Domain\ValueObject\TaskId;
use App\Tasks\Domain\DTO\TaskDTO;
use App\Tasks\Domain\DTO\TaskManagerDTO;
use App\Tasks\Domain\Entity\Task;
use App\Tasks\Domain\Entity\TaskManager;
use App\Tasks\Domain\Factory\TaskFactory;
use App\Tasks\Domain\Factory\TaskManagerFactory;

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
        $task = $taskManager->getTasks()->get(new TaskId($taskId));
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
            ProjectStatusFactory::scalarFromObject($taskManager->getStatus()),
            $taskManager->getOwner()->userId->value,
            $taskManager->getFinishDate()->getValue(),
            $taskManager->getParticipants()->getInnerItems(),
            $tasks->getInnerItems()
        );
        return $this->managerFactory->create($dto);
    }
}
