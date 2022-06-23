<?php
declare(strict_types=1);

namespace App\Tasks\Application\Handler;

use App\Projects\Domain\Repository\ProjectRepositoryInterface;
use App\Projects\Domain\ValueObject\ProjectId;
use App\Shared\Domain\Bus\Command\CommandHandlerInterface;
use App\Shared\Domain\Bus\Event\EventBusInterface;
use App\Shared\Domain\UuidGeneratorInterface;
use App\Shared\Domain\ValueObject\UserId;
use App\Tasks\Application\CQ\CreateTaskCommand;
use App\Tasks\Domain\ValueObject\TaskBrief;
use App\Tasks\Domain\ValueObject\TaskDescription;
use App\Tasks\Domain\ValueObject\TaskFinishDate;
use App\Tasks\Domain\ValueObject\TaskId;
use App\Tasks\Domain\ValueObject\TaskName;
use App\Tasks\Domain\ValueObject\TaskStartDate;
use App\Users\Domain\Repository\UserRepositoryInterface;

class CreateTaskCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly ProjectRepositoryInterface $projectRepository,
        private readonly UserRepositoryInterface $userRepository,
        private readonly UuidGeneratorInterface $uuidGenerator,
        private readonly EventBusInterface $eventBus,
    ) {
    }

    public function __invoke(CreateTaskCommand $command): void
    {
        $project = $this->projectRepository->getById(new ProjectId($command->projectId));
        $taskOwner = $this->userRepository->getById(new UserId($command->ownerId));

        $project->createTask(
            new TaskId($this->uuidGenerator->generate()),
            new TaskName($command->name),
            new TaskBrief($command->brief),
            new TaskDescription($command->description),
            new TaskStartDate($command->startDate),
            new TaskFinishDate($command->finishDate),
            $taskOwner,
            new UserId($command->currentUserId)
        );

        $this->projectRepository->update($project);
        $this->eventBus->dispatch(...$project->releaseEvents());
    }
}