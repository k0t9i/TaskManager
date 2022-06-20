<?php
declare(strict_types=1);

namespace App\Tasks\Application\Handler;

use App\Projects\Domain\Repository\ProjectRepositoryInterface;
use App\Projects\Domain\ValueObject\ProjectId;
use App\Shared\Domain\Bus\Command\CommandHandlerInterface;
use App\Shared\Domain\Bus\Event\EventBusInterface;
use App\Shared\Domain\UuidGeneratorInterface;
use App\Tasks\Application\CQ\UpdateTaskInformationCommand;
use App\Tasks\Domain\ValueObject\TaskBrief;
use App\Tasks\Domain\ValueObject\TaskDescription;
use App\Tasks\Domain\ValueObject\TaskFinishDate;
use App\Tasks\Domain\ValueObject\TaskId;
use App\Tasks\Domain\ValueObject\TaskName;
use App\Tasks\Domain\ValueObject\TaskStartDate;
use App\Users\Domain\ValueObject\UserId;

class UpdateTaskInformationCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly ProjectRepositoryInterface $projectRepository,
        private readonly UuidGeneratorInterface $uuidGenerator,
        private readonly EventBusInterface $eventBus,
    ) {
    }

    public function __invoke(UpdateTaskInformationCommand $command): void
    {
        $project = $this->projectRepository->getById(new ProjectId($command->projectId));

        $project->changeTaskInformation(
            new TaskId($this->uuidGenerator->generate()),
            new TaskName($command->name),
            new TaskBrief($command->brief),
            new TaskDescription($command->description),
            new TaskStartDate($command->startDate),
            new TaskFinishDate($command->finishDate),
            new UserId($command->currentUserId),
        );

        $this->projectRepository->update($project);
        $this->eventBus->dispatch(...$project->releaseEvents());
    }
}