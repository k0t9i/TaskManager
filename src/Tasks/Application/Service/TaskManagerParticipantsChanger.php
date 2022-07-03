<?php
declare(strict_types=1);

namespace App\Tasks\Application\Service;

use App\Requests\Domain\Factory\RequestStatusFactory;
use App\Shared\Domain\Collection\UserIdCollection;
use App\Shared\Domain\ValueObject\UserId;
use App\Tasks\Application\Factory\TaskManagerDTO;
use App\Tasks\Application\Factory\TaskManagerFactory;
use App\Tasks\Domain\Entity\TaskManager;

final class TaskManagerParticipantsChanger
{
    public function __construct(private readonly TaskManagerFactory $managerFactory)
    {
    }

    public function addParticipant(TaskManager $taskManager, string $participantId): TaskManager
    {
        $participants = $taskManager->getParticipantIds()->add(new UserId($participantId));
        return $this->createNewTaskManager($taskManager, $participants);
    }

    public function removeParticipant(TaskManager $taskManager, string $participantId): TaskManager
    {
        $participants = $taskManager->getParticipantIds()->add(new UserId($participantId));
        return $this->createNewTaskManager($taskManager, $participants);
    }

    private function createNewTaskManager(TaskManager $taskManager, UserIdCollection $participants): TaskManager
    {
        $dto = new TaskManagerDTO(
            $taskManager->getId()->value,
            $taskManager->getProjectId()->value,
            RequestStatusFactory::scalarFromObject($taskManager->getStatus()),
            $taskManager->getOwnerId()->value,
            $taskManager->getFinishDate()->getValue(),
            $participants,
            $taskManager->getTasks()
        );

        return $this->managerFactory->create($dto);
    }
}