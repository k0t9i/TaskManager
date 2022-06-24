<?php
declare(strict_types=1);

namespace App\ProjectTasks\Application\Handler;

use App\ProjectTasks\Application\CQ\CreateTaskCommand;
use App\ProjectTasks\Domain\Repository\ProjectTaskRepositoryInterface;
use App\ProjectTasks\Domain\ValueObject\ProjectTaskId;
use App\ProjectTasks\Domain\ValueObject\TaskBrief;
use App\ProjectTasks\Domain\ValueObject\TaskDescription;
use App\ProjectTasks\Domain\ValueObject\TaskId;
use App\ProjectTasks\Domain\ValueObject\TaskInformation;
use App\ProjectTasks\Domain\ValueObject\TaskName;
use App\Shared\Domain\Bus\Command\CommandHandlerInterface;
use App\Shared\Domain\Bus\Event\EventBusInterface;
use App\Shared\Domain\UuidGeneratorInterface;
use App\Shared\Domain\ValueObject\DateTime;
use App\Shared\Domain\ValueObject\UserId;
use App\Users\Domain\Repository\UserRepositoryInterface;

class CreateTaskCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly ProjectTaskRepositoryInterface $projectRepository,
        private readonly UserRepositoryInterface $userRepository,
        private readonly UuidGeneratorInterface $uuidGenerator,
        private readonly EventBusInterface $eventBus,
    ) {
    }

    public function __invoke(CreateTaskCommand $command): void
    {
        $project = $this->projectRepository->findById(new ProjectTaskId($command->projectId));
        $taskOwner = $this->userRepository->getById(new UserId($command->ownerId));

        $project->createTask(
            new TaskId($this->uuidGenerator->generate()),
            new TaskInformation(
                new TaskName($command->name),
                new TaskBrief($command->brief),
                new TaskDescription($command->description),
                new DateTime($command->startDate),
                new DateTime($command->finishDate)
            ),
            $taskOwner->getId(),
            new UserId($command->currentUserId)
        );

        $this->projectRepository->update($project);
        $this->eventBus->dispatch(...$project->releaseEvents());
    }
}