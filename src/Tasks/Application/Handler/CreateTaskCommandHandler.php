<?php
declare(strict_types=1);

namespace App\Tasks\Application\Handler;

use App\Projects\Domain\Repository\ProjectRepositoryInterface;
use App\Shared\Domain\Bus\Command\CommandHandlerInterface;
use App\Shared\Domain\Bus\Event\EventBusInterface;
use App\Shared\Domain\Exception\ProjectNotExistException;
use App\Shared\Domain\Exception\UserNotExistException;
use App\Shared\Domain\UuidGeneratorInterface;
use App\Shared\Domain\ValueObject\DateTime;
use App\Shared\Domain\ValueObject\ProjectId;
use App\Shared\Domain\ValueObject\TaskId;
use App\Shared\Domain\ValueObject\UserId;
use App\Tasks\Application\Command\CreateTaskCommand;
use App\Tasks\Domain\Entity\Task;
use App\Tasks\Domain\Entity\TaskProject;
use App\Tasks\Domain\Repository\TaskRepositoryInterface;
use App\Tasks\Domain\ValueObject\TaskBrief;
use App\Tasks\Domain\ValueObject\TaskDescription;
use App\Tasks\Domain\ValueObject\TaskInformation;
use App\Tasks\Domain\ValueObject\TaskName;
use App\Tasks\Domain\ValueObject\TaskProjectId;
use App\Users\Domain\Repository\UserRepositoryInterface;

class CreateTaskCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly TaskRepositoryInterface $taskRepository,
        private readonly ProjectRepositoryInterface $projectRepository,
        private readonly UserRepositoryInterface $userRepository,
        private readonly UuidGeneratorInterface $uuidGenerator,
        private readonly EventBusInterface $eventBus,
    ) {
    }

    public function __invoke(CreateTaskCommand $command): void
    {
        $project = $this->projectRepository->findById(new ProjectId($command->projectId));
        if ($project === null) {
            throw new ProjectNotExistException();
        }
        $owner = $this->userRepository->findById(new UserId($command->ownerId));
        if ($owner === null) {
            throw new UserNotExistException();
        }

        $task = Task::create(
            new TaskId($this->uuidGenerator->generate()),
            $project->getId(),
            new TaskInformation(
                new TaskName($command->name),
                new TaskBrief($command->brief),
                new TaskDescription($command->description),
                new DateTime($command->startDate),
                new DateTime($command->finishDate)
            ),
            $owner->getId(),
            new TaskProject(
                new TaskProjectId($this->uuidGenerator->generate()),
                $project->getStatus(),
                $project->getOwner()->userId,
                $project->getInformation()->finishDate,
                $project->getParticipants()->copyInnerCollection()
            ),
            new UserId($command->currentUserId)
        );

        $this->taskRepository->save($task);
        $this->eventBus->dispatch(...$task->releaseEvents());
    }
}